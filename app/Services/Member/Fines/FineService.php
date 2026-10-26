<?php
namespace App\Services\Member\Fines;
use App\Models\Fines\Fine;
use App\Models\User;
class FineService {
 public function paginate(User $member,array $f){
  return Fine::with(['loanRequestItem.item:id,unit_code,name','loanRequestItem.loanRequest:id,status,handed_over_at,due_at,returned_at'])
   ->whereHas('loanRequestItem.loanRequest',fn($q)=>$q->where('member_id',$member->id))
   ->when(isset($f['status']),fn($q)=>$q->where('status',$f['status']))
   ->orderByDesc('assessed_at')->orderByDesc('id')->paginate(20)->withQueryString();
 }
}
