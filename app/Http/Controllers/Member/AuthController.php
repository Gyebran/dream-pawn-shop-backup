<?php
namespace App\Http\Controllers\Member;
use App\Http\Controllers\Controller;
use App\Http\Requests\Member\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller {
 public function showLoginForm(Request $request): View|RedirectResponse {
  if (Auth::check() && Auth::user()->isMember() && Auth::user()->memberProfile()->exists()) {
   return redirect()->route('member.dashboard');
  }
  return view('member.auth.login');
 }

 public function login(LoginRequest $request): JsonResponse|RedirectResponse {
  if(!Auth::attempt($request->safe()->only(['email','password'])))throw ValidationException::withMessages(['email'=>['Email atau password tidak valid.']]);
  $request->session()->regenerate();$u=$request->user();
  if(!$u->isMember()||!$u->memberProfile()->exists()||!$u->is_active){Auth::logout();$request->session()->invalidate();$request->session()->regenerateToken();
   if ($request->wantsJson()) {
    return response()->json(['message'=>'Akun ini bukan Member aktif.'],403);
   }
   return redirect()->route('member.login.view')->withErrors(['email'=>'Akun ini bukan Member aktif atau sedang dinonaktifkan.']);
  }
  if ($request->wantsJson()) {
   return response()->json(['message'=>'Login Member berhasil.','data'=>['id'=>$u->id,'name'=>$u->name,'email'=>$u->email,'role'=>$u->role]]);
  }
  return redirect()->route('member.dashboard')->with('success','Selamat datang kembali di Dream Pawn Shop, '.$u->name.'!');
 }

 public function logout(Request $request): JsonResponse|RedirectResponse {
  Auth::logout();$request->session()->invalidate();$request->session()->regenerateToken();
  if ($request->wantsJson()) {
   return response()->json(['message'=>'Logout berhasil.']);
  }
  return redirect()->route('member.login.view')->with('success','Logout berhasil.');
 }
}
