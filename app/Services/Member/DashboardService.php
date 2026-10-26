<?php
namespace App\Services\Member;
use App\Models\Fines\Fine;
use App\Models\Loans\LoanRequest;
use App\Models\User;
use App\Services\Loans\LoanEligibilityService;
class DashboardService {
 public function __construct(private readonly LoanEligibilityService $eligibility){}
 public function summary(User $member):array{
  $allocated=$this->eligibility->allocatedUnitCount($member->id);
  $pending=$this->eligibility->pendingUnitCount($member->id);
  $remaining=max(0,LoanEligibilityService::MAX_ALLOCATED_UNITS-$allocated-$pending);
  $unpaid=Fine::whereHas('loanRequestItem.loanRequest',fn($q)=>$q->where('member_id',$member->id))->where('status',Fine::UNPAID);
  $unpaidCount=(clone $unpaid)->count();$unpaidAmount=(int)(clone $unpaid)->sum('amount');
  $counts=LoanRequest::where('member_id',$member->id)->selectRaw('status, COUNT(*) AS total')->groupBy('status')->pluck('total','status');
  $overdue=LoanRequest::where('member_id',$member->id)->where('status',LoanRequest::ACTIVE)->where('due_at','<',now())->count();
  $reasons=[];
  if(!$member->isMember())$reasons[]='Akun tidak aktif.';
  if($unpaidCount>0)$reasons[]='Ada denda yang belum lunas.';
  if($remaining===0)$reasons[]='Batas maksimal '.LoanEligibilityService::MAX_ALLOCATED_UNITS.' unit sudah tercapai.';
  return [
   'member'=>['id'=>$member->id,'name'=>$member->name,'email'=>$member->email],
   'allocation'=>['max_units'=>LoanEligibilityService::MAX_ALLOCATED_UNITS,'allocated_units'=>$allocated,'pending_units'=>$pending,'remaining_units'=>$remaining],
   'fines'=>['unpaid_count'=>$unpaidCount,'unpaid_amount'=>$unpaidAmount],
   'requests'=>['pending'=>(int)($counts[LoanRequest::PENDING]??0),'approved'=>(int)($counts[LoanRequest::APPROVED]??0),'active'=>(int)($counts[LoanRequest::ACTIVE]??0),'overdue'=>$overdue,'returned'=>(int)($counts[LoanRequest::RETURNED]??0)],
   'eligibility'=>['can_request'=>$reasons===[],'reasons'=>$reasons],
  ];
 }
}
