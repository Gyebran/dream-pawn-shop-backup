<?php
namespace App\Http\Controllers\Member;
use App\Http\Controllers\Controller;
use App\Services\Member\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller {
 public function __invoke(Request $request,DashboardService $service): JsonResponse|View {
  $data = $service->summary($request->user());
  if ($request->wantsJson()) {
   return response()->json(['data'=>$data]);
  }
  return view('member.dashboard', ['data' => $data]);
 }
}
