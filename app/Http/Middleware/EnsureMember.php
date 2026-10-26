<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class EnsureMember {
 public function handle(Request $request, Closure $next): Response {
  $user=$request->user();
  if (!$user) {
   if ($request->expectsJson()) {
    return response()->json(['message' => 'Unauthenticated.'], 401);
   }
   return redirect()->guest(route('member.login.view'));
  }
  if (!$user->isMember() || !$user->memberProfile()->exists()) {
   if ($request->expectsJson()) {
    return response()->json(['message'=>'Akses Member diperlukan.'],403);
   }
   return response()->view('errors.403', [
    'title' => 'Akses Member Diperlukan',
    'message' => 'Halaman ini khusus diperuntukkan bagi akun Member. Anda saat ini sedang login dengan akun ' . ucfirst($user->role) . ' (' . $user->email . ').',
    'currentUser' => $user,
    'requiredRole' => 'member',
   ], 403);
  }
  return $next($request);
 }
}
