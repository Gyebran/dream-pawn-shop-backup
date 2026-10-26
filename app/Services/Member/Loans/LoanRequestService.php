<?php
namespace App\Services\Member\Loans;
use App\Models\Catalog\Item;
use App\Models\Fines\Fine;
use App\Models\Loans\LoanRequest;
use App\Models\User;
use App\Services\Loans\LoanEligibilityService;
use Illuminate\Support\Facades\DB;
class LoanRequestService {
 private const WITH=['details.item:id,unit_code,name,status','details.fine','reviewer:id,name','returnConfirmer:id,name'];
 public function __construct(private readonly LoanEligibilityService $eligibility){}
 public function paginate(User $member,array $f){
  return LoanRequest::with(self::WITH)->where('member_id',$member->id)
   ->when(isset($f['status']),fn($q)=>$q->where('status',$f['status']))
   ->orderByDesc('requested_at')->orderByDesc('id')->paginate(20)->withQueryString()
   ->through(fn(LoanRequest $l)=>$this->decorate($l));
 }
 /** Requests of other Members are reported as not found so their existence is never revealed (BR-18). */
 public function show(User $member,LoanRequest $loan):LoanRequest{if((int)$loan->member_id!==(int)$member->id)abort(404);return $this->decorate($loan->load(self::WITH));}
 /**
  * Flowchart 5.1 "Memenuhi syarat?": active account, no unpaid fines, all units available, and the two-unit cap.
  * Units stay `available` until Admin approval reserves them (D-02); submission only records the request.
  */
 public function create(User $member,array $itemIds):array{
  $ids=array_values(array_unique(array_map('intval',$itemIds)));
  return DB::transaction(function()use($member,$ids){
   $m=User::whereKey($member->id)->lockForUpdate()->firstOrFail();
   if(!$m->isMember()||!$m->memberProfile()->exists())return $this->error('Akun Member tidak aktif atau tidak memenuhi syarat.',403);
   if($this->eligibility->hasUnpaidFines($m->id))return $this->error('Anda memiliki denda yang belum lunas. Lunasi denda terlebih dahulu sebelum mengajukan peminjaman baru.',422);
   $items=Item::whereIn('id',$ids)->orderBy('id')->lockForUpdate()->get();
   if($items->count()!==count($ids))return $this->error('Satu atau lebih unit tidak ditemukan.',422);
   $unavailable=$items->filter(fn(Item $i)=>$i->status!==Item::AVAILABLE);
   if($unavailable->isNotEmpty())return $this->error('Unit tidak tersedia: '.$unavailable->pluck('unit_code')->implode(', ').'.',422);
   $pending=$this->eligibility->pendingUnitCount($m->id);
   if(!$this->eligibility->canAllocate($m->id,count($ids)+$pending))return $this->error('Batas maksimal '.LoanEligibilityService::MAX_ALLOCATED_UNITS.' unit akan terlampaui (termasuk pengajuan yang masih menunggu, reservasi, dan peminjaman aktif).',422);
   $now=now();
   $loan=LoanRequest::create(['member_id'=>$m->id,'status'=>LoanRequest::PENDING,'requested_at'=>$now]);
   foreach($ids as $id)$loan->details()->create(['item_id'=>$id,'created_at'=>$now]);
   return ['data'=>$this->decorate($loan->fresh()->load(self::WITH))];
  });
 }
 /** Adds read-only helper fields for the Member UI; the stored fine remains the only authoritative amount. */
 private function decorate(LoanRequest $l):LoanRequest{
  $now=now();$units=$l->details->count();
  $overdueSeconds=($l->status===LoanRequest::ACTIVE&&$l->due_at&&$now->gt($l->due_at))?$now->getTimestamp()-$l->due_at->getTimestamp():0;
  $days=intdiv($overdueSeconds,86400);
  $l->setAttribute('unit_count',$units);
  $l->setAttribute('is_overdue',$overdueSeconds>0);
  $l->setAttribute('overdue_full_days',$days);
  $l->setAttribute('estimated_fine',$days*Fine::RATE_PER_UNIT_PER_DAY*$units);
  $l->setAttribute('total_fine',(int)$l->details->sum(fn($d)=>$d->fine?->amount??0));
  $l->setAttribute('unpaid_fine',(int)$l->details->sum(fn($d)=>($d->fine&&$d->fine->status===Fine::UNPAID)?$d->fine->amount:0));
  return $l;
 }
 private function error(string $message,int $status):array{return ['error'=>$message,'http_status'=>$status];}
}
