<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Loans\LoanRequest;
use App\Services\Admin\Loans\LoanWorkflowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LoanRequestController extends Controller {
 public function index(Request $r,LoanWorkflowService $s): JsonResponse|View {
  $f=$r->validate(['status'=>['sometimes','nullable',Rule::in([LoanRequest::PENDING,LoanRequest::APPROVED,LoanRequest::REJECTED,LoanRequest::CANCELLED,LoanRequest::ACTIVE,LoanRequest::RETURNED])],'member_id'=>['sometimes','nullable','integer','exists:members,user_id']]);
  $f=array_filter($f,fn($v)=>$v!==null&&$v!=='');
  $data = $s->paginate($f);
  if ($r->wantsJson()) {
   return response()->json(['data'=>$data]);
  }
  return view('admin.loan-requests.index', ['loans' => $data, 'filters' => $f]);
 }
 public function approve(LoanRequest $loan,LoanWorkflowService $s): JsonResponse|RedirectResponse {
  return $this->respond($s->approve($loan,(int)auth()->id()),'Permintaan disetujui; barang telah direservasi.');
 }
 public function reject(LoanRequest $loan,LoanWorkflowService $s): JsonResponse|RedirectResponse {
  return $this->respond($s->reject($loan,(int)auth()->id()),'Permintaan ditolak.');
 }
 public function handover(LoanRequest $loan,LoanWorkflowService $s): JsonResponse|RedirectResponse {
  return $this->respond($s->handover($loan),'Serah terima dicatat.');
 }
 public function confirmReturn(LoanRequest $loan,LoanWorkflowService $s): JsonResponse|RedirectResponse {
  return $this->respond($s->confirmReturn($loan,(int)auth()->id()),'Pengembalian dikonfirmasi.');
 }
 private function respond(array $r,string $message): JsonResponse|RedirectResponse {
  if(isset($r['error'])) {
   if (request()->wantsJson()) {
    return response()->json(['message'=>$r['error']],$r['http_status']??422);
   }
   return redirect()->back()->withErrors(['loan' => $r['error']]);
  }
  if (request()->wantsJson()) {
   return response()->json(['message'=>$message,'data'=>$r['data']]);
  }
  return redirect()->route('admin.loan-requests.index')->with('success', $message);
 }
}