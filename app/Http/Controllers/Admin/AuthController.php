<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller {
 public function showLoginForm(Request $request): View|RedirectResponse {
  if (Auth::check() && Auth::user()->isAdmin() && Auth::user()->adminProfile()->exists()) {
   return redirect()->route('admin.dashboard');
  }
  return view('admin.auth.login');
 }

 public function login(LoginRequest $request): JsonResponse|RedirectResponse {
  if(!Auth::attempt($request->safe()->only(['email','password'])))throw ValidationException::withMessages(['email'=>['Email atau password tidak valid.']]);
  $request->session()->regenerate();$u=$request->user();
  if(!$u->isAdmin()||!$u->adminProfile()->exists()){Auth::logout();$request->session()->invalidate();$request->session()->regenerateToken();
   if ($request->wantsJson()) {
    return response()->json(['message'=>'Akun ini tidak memiliki akses Admin atau sedang nonaktif.'],403);
   }
   return redirect()->route('admin.login.view')->withErrors(['email'=>'Akun ini tidak memiliki akses Admin atau sedang nonaktif.']);
  }
  if ($request->wantsJson()) {
   return response()->json(['message'=>'Login Admin berhasil.','data'=>['id'=>$u->id,'name'=>$u->name,'email'=>$u->email,'role'=>$u->role]]);
  }
  return redirect()->route('admin.dashboard')->with('success','Selamat datang kembali, '.$u->name.'!');
 }

 public function logout(Request $request): JsonResponse|RedirectResponse {
  Auth::logout();$request->session()->invalidate();$request->session()->regenerateToken();
  if ($request->wantsJson()) {
   return response()->json(['message'=>'Logout berhasil.']);
  }
  return redirect()->route('admin.login.view')->with('success','Logout berhasil.');
 }
}