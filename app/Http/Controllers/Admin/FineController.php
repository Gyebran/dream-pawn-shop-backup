<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Fines\Fine;
use App\Services\Admin\Fines\FineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FineController extends Controller {
 public function index(Request $r,FineService $s): JsonResponse|View {
  $f=$r->validate(['status'=>['sometimes','nullable',Rule::in([Fine::UNPAID,Fine::PAID])],'member_id'=>['sometimes','nullable','integer','exists:members,user_id']]);
  $f=array_filter($f,fn($v)=>$v!==null&&$v!=='');
  $data = $s->paginate($f);
  if ($r->wantsJson()) {
   return response()->json(['data'=>$data]);
  }
  return view('admin.fines.index', ['fines' => $data, 'filters' => $f]);
 }
 public function confirmPayment(Fine $fine,FineService $s): JsonResponse|RedirectResponse {
  $data = $s->confirmPayment($fine,auth()->id());
  if (request()->wantsJson()) {
   return response()->json(['message'=>'Pembayaran denda dikonfirmasi.','data'=>$data]);
  }
  return redirect()->route('admin.fines.index')->with('success', 'Pembayaran denda #'.$fine->id.' telah dikonfirmasi lunas.');
 }
}