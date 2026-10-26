<?php
namespace App\Services\Admin\Loans;
use App\Models\Catalog\Item;
use App\Models\Fines\Fine;
use App\Models\Loans\LoanRequest;
use App\Models\User;
use App\Services\Loans\LoanEligibilityService;
use Illuminate\Support\Facades\DB;
class LoanWorkflowService {
 public function __construct(private readonly LoanEligibilityService $eligibility){}
 public function paginate(array $f){return LoanRequest::with(['member:id,name,email,is_active','details.item','details.fine','reviewer:id,name','returnConfirmer:id,name'])->when(isset($f['status']),fn($q)=>$q->where('status',$f['status']))->when(isset($f['member_id']),fn($q)=>$q->where('member_id',$f['member_id']))->orderByDesc('requested_at')->paginate(20)->withQueryString();}
 public function approve(LoanRequest $request,int $adminId):array{return DB::transaction(function()use($request,$adminId){
  $loan=LoanRequest::whereKey($request->id)->lockForUpdate()->firstOrFail();if($loan->status!==LoanRequest::PENDING)return $this->error('Hanya permintaan pending yang bisa disetujui.',409);
  $member=User::whereKey($loan->member_id)->lockForUpdate()->firstOrFail();if(!$member->isMember()||!$member->memberProfile()->exists())return $this->error('Akun Member tidak aktif atau tidak valid.',422);
  if($this->eligibility->hasUnpaidFines($member->id))return $this->error('Member memiliki denda yang belum lunas.',422);
  $details=$loan->details()->orderBy('item_id')->get();if($details->isEmpty()||$details->pluck('item_id')->unique()->count()!==$details->count())return $this->error('Permintaan kosong atau memuat barang duplikat.',422);
  $items=Item::whereIn('id',$details->pluck('item_id'))->orderBy('id')->lockForUpdate()->get();if($items->count()!==$details->count()||$items->contains(fn($i)=>$i->status!==Item::AVAILABLE))return $this->error('Satu atau lebih barang tidak tersedia.',409);
  if(!$this->eligibility->canAllocate($member->id,$details->count()))return $this->error('Batas maksimal dua unit yang direservasi atau dipinjam akan terlampaui.',422);
  Item::whereIn('id',$items->pluck('id'))->update(['status'=>Item::RESERVED]);$now=now();$loan->update(['status'=>LoanRequest::APPROVED,'reviewed_by'=>$adminId,'reviewed_at'=>$now,'handover_deadline_at'=>$now->copy()->addDay()]);
  return ['data'=>$loan->fresh()->load(['details.item','member:id,name,email'])];
 });}
 public function reject(LoanRequest $request,int $adminId):array{return DB::transaction(function()use($request,$adminId){$l=LoanRequest::whereKey($request->id)->lockForUpdate()->firstOrFail();if($l->status!==LoanRequest::PENDING)return $this->error('Hanya permintaan pending yang bisa ditolak.',409);$l->update(['status'=>LoanRequest::REJECTED,'reviewed_by'=>$adminId,'reviewed_at'=>now()]);return ['data'=>$l->fresh()];});}
 public function handover(LoanRequest $request):array{return DB::transaction(function()use($request){
  $l=LoanRequest::whereKey($request->id)->lockForUpdate()->firstOrFail();if($l->status!==LoanRequest::APPROVED)return $this->error('Hanya permintaan approved yang bisa diserahterimakan.',409);
  $details=$l->details()->with('item')->get();
  if($l->handover_deadline_at&&$l->handover_deadline_at->lte(now())){if($details->isEmpty()||$details->contains(fn($d)=>!$d->item||$d->item->status!==Item::RESERVED))return $this->error('Status reservasi barang tidak konsisten; pembatalan otomatis perlu diperiksa Admin.',409);foreach($details as $d)$d->item->update(['status'=>Item::AVAILABLE]);$l->update(['status'=>LoanRequest::CANCELLED]);return $this->error('Batas serah terima terlewat; permintaan dibatalkan dan barang dilepas.',409);}
  if($details->isEmpty()||$details->contains(fn($d)=>!$d->item||$d->item->status!==Item::RESERVED))return $this->error('Status reservasi barang tidak konsisten.',409);
  foreach($details as $d)$d->item->update(['status'=>Item::LOANED]);$now=now();$l->update(['status'=>LoanRequest::ACTIVE,'handed_over_at'=>$now,'due_at'=>$now->copy()->addDays(5)]);return ['data'=>$l->fresh()->load(['details.item','member:id,name,email'])];
 });}
 public function confirmReturn(LoanRequest $request,int $adminId):array{return DB::transaction(function()use($request,$adminId){
  $l=LoanRequest::whereKey($request->id)->lockForUpdate()->firstOrFail();if($l->status!==LoanRequest::ACTIVE||!$l->due_at)return $this->error('Hanya peminjaman aktif yang dapat dikonfirmasi pengembaliannya.',409);
  $now=now();$lateSeconds=max(0,$now->getTimestamp()-$l->due_at->getTimestamp());$latePeriods=intdiv($lateSeconds,86400);$details=$l->details()->with('item','fine')->get();
  if($details->isEmpty()||$details->contains(fn($d)=>!$d->item||$d->item->status!==Item::LOANED))return $this->error('Status barang tidak konsisten dengan peminjaman aktif.',409);
  foreach($details as $d){$d->item->update(['status'=>Item::AVAILABLE]);if($latePeriods>0&&!$d->fine)Fine::create(['loan_request_item_id'=>$d->id,'recorded_by'=>$adminId,'amount'=>$latePeriods*10000,'status'=>Fine::UNPAID,'assessed_at'=>$now]);}
  $l->update(['status'=>LoanRequest::RETURNED,'returned_at'=>$now,'return_confirmed_by'=>$adminId]);return ['data'=>$l->fresh()->load(['details.item','details.fine'])];
 });}
 private function error(string $message,int $status):array{return ['error'=>$message,'http_status'=>$status];}
}