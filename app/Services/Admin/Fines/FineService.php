<?php
namespace App\Services\Admin\Fines;
use App\Models\Fines\Fine;
use Illuminate\Support\Facades\DB;
class FineService {
 public function paginate(array $f){return Fine::with(['loanRequestItem.item','loanRequestItem.loanRequest.member:id,name,email','recorder:id,name','paymentConfirmer:id,name'])->when(isset($f['status']),fn($q)=>$q->where('status',$f['status']))->orderByDesc('assessed_at')->paginate(20)->withQueryString();}
 public function confirmPayment(Fine $fine,int $adminId):array{return DB::transaction(function()use($fine,$adminId){$f=Fine::whereKey($fine->id)->lockForUpdate()->firstOrFail();if($f->status!==Fine::UNPAID)return ['error'=>'Denda ini tidak berstatus unpaid.','http_status'=>409];$f->update(['status'=>Fine::PAID,'paid_at'=>now(),'paid_confirmed_by'=>$adminId]);return ['data'=>$f->fresh()->load('paymentConfirmer:id,name')];});}
}