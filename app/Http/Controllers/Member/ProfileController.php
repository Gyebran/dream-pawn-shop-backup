<?php
namespace App\Http\Controllers\Member;
use App\Http\Controllers\Controller;
use App\Http\Requests\Member\UpdateProfileRequest;
use App\Services\Member\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller {
 public function show(Request $r,ProfileService $s): JsonResponse|View {
  $data = $s->show($r->user());
  if ($r->wantsJson()) {
   return response()->json(['data'=>$data]);
  }
  return view('member.profile.show', ['user' => $data]);
 }
 public function update(UpdateProfileRequest $r,ProfileService $s): JsonResponse|RedirectResponse {
  $data = $s->update($r->user(),$r->validated());
  if ($r->wantsJson()) {
   return response()->json(['message'=>'Profil diperbarui.','data'=>$data]);
  }
  return redirect()->route('member.profile.show')->with('success', 'Profil Anda berhasil diperbarui.');
 }
}
