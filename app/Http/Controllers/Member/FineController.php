<?php
namespace App\Http\Controllers\Member;
use App\Http\Controllers\Controller;
use App\Models\Fines\Fine;
use App\Services\Member\Fines\FineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FineController extends Controller {
 public function index(Request $r,FineService $s): JsonResponse|View {
  $f=$r->validate(['status'=>['sometimes','nullable',Rule::in([Fine::UNPAID,Fine::PAID])]]);
  $f=array_filter($f,fn($v)=>$v!==null&&$v!=='');
  $data = $s->paginate($r->user(),$f);
  if ($r->wantsJson()) {
   return response()->json(['data'=>$data]);
  }
  return view('member.fines.index', ['fines' => $data, 'filters' => $f]);
 }
}
