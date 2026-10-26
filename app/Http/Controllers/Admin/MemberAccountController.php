<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMemberRequest;
use App\Http\Requests\Admin\UpdateMemberRequest;
use App\Models\User;
use App\Services\Admin\MemberAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberAccountController extends Controller {
 public function index(Request $r,MemberAccountService $s): JsonResponse|View {
  $f=$r->validate(['is_active'=>['sometimes','boolean'],'search'=>['sometimes','string','max:100']]);
  $data = $s->paginate($f);
  if ($r->wantsJson()) {
   return response()->json(['data'=>$data]);
  }
  return view('admin.members.index', ['members' => $data, 'filters' => $f]);
 }
 public function store(StoreMemberRequest $r,MemberAccountService $s): JsonResponse|RedirectResponse {
  $data = $s->create($r->validated());
  if ($r->wantsJson()) {
   return response()->json(['message'=>'Akun Member dibuat.','data'=>$data],201);
  }
  return redirect()->route('admin.members.index')->with('success', 'Akun Member "'.$data->name.'" ('.$data->email.') berhasil dibuat.');
 }
 public function show(User $member,MemberAccountService $s): JsonResponse|View {
  $data = $s->show($member);
  if (request()->wantsJson()) {
   return response()->json(['data'=>$data]);
  }
  return view('admin.members.show', ['member' => $data]);
 }
 public function update(UpdateMemberRequest $r,User $member,MemberAccountService $s): JsonResponse|RedirectResponse {
  $data = $s->update($member,$r->validated());
  if ($r->wantsJson()) {
   return response()->json(['message'=>'Akun Member diperbarui.','data'=>$data]);
  }
  return redirect()->route('admin.members.index')->with('success', 'Akun Member "'.$data->name.'" berhasil diperbarui.');
 }
}