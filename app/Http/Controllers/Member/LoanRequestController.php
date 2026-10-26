<?php
namespace App\Http\Controllers\Member;
use App\Http\Controllers\Controller;
use App\Http\Requests\Member\StoreLoanRequestRequest;
use App\Models\Loans\LoanRequest;
use App\Services\Member\Loans\LoanRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LoanRequestController extends Controller {
 public function index(Request $r,LoanRequestService $s): JsonResponse|View {
  $f=$r->validate(['status'=>['sometimes','nullable',Rule::in([LoanRequest::PENDING,LoanRequest::APPROVED,LoanRequest::REJECTED,LoanRequest::CANCELLED,LoanRequest::ACTIVE,LoanRequest::RETURNED])]]);
  $f=array_filter($f,fn($v)=>$v!==null&&$v!=='');
  $data = $s->paginate($r->user(),$f);
  if ($r->wantsJson()) {
   return response()->json(['data'=>$data]);
  }
  return view('member.loan-requests.index', ['loans' => $data, 'filters' => $f]);
 }
 public function store(StoreLoanRequestRequest $r,LoanRequestService $s): JsonResponse|RedirectResponse {
  $res=$s->create($r->user(),$r->validated()['item_ids']);
  if(isset($res['error'])) {
   if ($r->wantsJson()) {
    return response()->json(['message'=>$res['error']],$res['http_status']??422);
   }
   return redirect()->back()->withErrors(['item_ids' => $res['error']]);
  }
  if ($r->wantsJson()) {
   return response()->json(['message'=>'Pengajuan peminjaman dikirim dan menunggu tinjauan Admin.','data'=>$res['data']],201);
  }
  return redirect()->route('member.loan-requests.index')->with('success', 'Pengajuan peminjaman berhasil dikirim dan menunggu tinjauan Admin.');
 }
 public function show(Request $r,LoanRequest $loan,LoanRequestService $s): JsonResponse|View {
  $data = $s->show($r->user(),$loan);
  if ($r->wantsJson()) {
   return response()->json(['data'=>$data]);
  }
  return view('member.loan-requests.show', ['loan' => $data]);
 }
}
