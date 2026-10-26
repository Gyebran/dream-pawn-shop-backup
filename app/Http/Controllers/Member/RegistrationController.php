<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\RegisterMemberRequest;
use App\Models\Member\Member;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            return Auth::user()->isMember() && Auth::user()->memberProfile()->exists()
                ? redirect()->route('member.dashboard')
                : redirect()->route('home');
        }

        return view('member.auth.register');
    }

    public function store(RegisterMemberRequest $request): RedirectResponse
    {
        $data = $request->safe()->only(['name', 'email', 'password']);

        DB::transaction(function () use ($data): void {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => 'member',
                'is_active' => true,
            ]);

            Member::create(['user_id' => $user->id]);
        });

        return redirect()
            ->route('member.login.view')
            ->with('success', 'Registrasi berhasil. Silakan masuk menggunakan akun yang baru dibuat.');
    }
}
