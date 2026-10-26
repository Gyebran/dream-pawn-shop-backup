<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class EnsureAdmin {
 public function handle(Request $request, Closure $next): Response {
  $user=$request->user();
  if (!$user) {
   if ($request->expectsJson()) {
    return response()->json(['message' => 'Unauthenticated.'], 401);
   }
   return redirect()->guest(route('admin.login.view'));
  }
  if (!$user->isAdmin() || !$user->adminProfile()->exists()) {
   if ($request->expectsJson()) {
    return response()->json(['message'=>'Admin access required.'],403);
   }
   return response()->view('errors.403', [
    'title' => 'Akses Admin Diperlukan',
    'message' => 'Halaman ini khusus diperuntukkan bagi Admin & Petugas Toko. Anda saat ini sedang login dengan akun ' . ucfirst($user->role) . ' (' . $user->email . ').',
    'currentUser' => $user,
    'requiredRole' => 'admin',
   ], 403);
  }
  return $next($request);
 }
}