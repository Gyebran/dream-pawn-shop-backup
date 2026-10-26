<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MemberRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_member_registration_page(): void
    {
        $this->get('/member/register')
            ->assertOk()
            ->assertSee('Buat Akun Member')
            ->assertSee(route('member.register.store'), false);

        $this->get('/member/login')
            ->assertOk()
            ->assertSee(route('member.register'), false);
    }

    public function test_registration_creates_active_member_and_hashes_password(): void
    {
        $response = $this->post('/member/register', [
            'name' => 'Chronos Borrower',
            'email' => 'chronos@example.test',
            'password' => 'secure-password-123',
            'password_confirmation' => 'secure-password-123',
        ]);

        $response->assertRedirect(route('member.login.view'))
            ->assertSessionHas('success');

        $user = User::where('email', 'chronos@example.test')->firstOrFail();

        $this->assertSame('member', $user->role);
        $this->assertTrue($user->is_active);
        $this->assertTrue(Hash::check('secure-password-123', $user->password));
        $this->assertDatabaseHas('members', ['user_id' => $user->id]);
        $this->assertDatabaseMissing('admins', ['user_id' => $user->id]);
    }

    public function test_registration_rejects_duplicate_email(): void
    {
        User::create([
            'name' => 'Existing Member',
            'email' => 'existing@example.test',
            'password' => 'secure-password-123',
            'role' => 'member',
            'is_active' => true,
        ]);

        $this->from('/member/register')->post('/member/register', [
            'name' => 'Another Member',
            'email' => 'existing@example.test',
            'password' => 'secure-password-123',
            'password_confirmation' => 'secure-password-123',
        ])->assertRedirect('/member/register')->assertSessionHasErrors('email');

        $this->assertDatabaseCount('users', 1);
    }

    public function test_registration_rejects_weak_or_mismatched_passwords(): void
    {
        $this->from('/member/register')->post('/member/register', [
            'name' => 'New Member',
            'email' => 'new@example.test',
            'password' => 'short',
            'password_confirmation' => 'different',
        ])->assertRedirect('/member/register')->assertSessionHasErrors('password');

        $this->assertDatabaseCount('users', 0);
    }
}
