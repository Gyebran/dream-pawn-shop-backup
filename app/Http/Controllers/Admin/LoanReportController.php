<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Loans\LoanRequest;
use App\Services\Admin\Reports\LoanReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LoanReportController extends Controller {
 private function filters(Request $r):array{
  $f=$r->validate(['status'=>['sometimes','nullable',Rule::in([LoanRequest::PENDING,LoanRequest::APPROVED,LoanRequest::REJECTED,LoanRequest::CANCELLED,LoanRequest::ACTIVE,LoanRequest::RETURNED])],'member_id'=>['sometimes','nullable','integer','exists:members,user_id'],'from'=>['sometimes','nullable','date'],'to'=>['sometimes','nullable','date']]);
  $f=array_filter($f,fn($v)=>$v!==null&&$v!=='');
  if(isset($f['from'],$f['to'])&&Carbon::parse($f['to'])->lt(Carbon::parse($f['from'])))throw ValidationException::withMessages(['to'=>['Tanggal akhir tidak boleh sebelum tanggal awal.']]);
  return $f;
 }
 public function index(Request $r,LoanReportService $s): JsonResponse|View {
  $f = $this->filters($r);
  $data = $s->paginate($f);
  if ($r->wantsJson()) {
   return response()->json(['data'=>$data]);
  }
  return view('admin.reports.loans', ['report' => $data, 'filters' => $f]);
 }
 public function export(Request $r,LoanReportService $s):StreamedResponse{return $s->exportCsv($this->filters($r));}
 public function print(Request $r,LoanReportService $s):View{return view('admin.reports.loans-print',['report'=>$s->forPrint($this->filters($r)),'filters'=>$this->filters($r)]);}
}