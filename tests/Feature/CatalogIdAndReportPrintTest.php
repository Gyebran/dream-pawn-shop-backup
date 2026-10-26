<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CatalogIdAndReportPrintTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $user = User::create([
            'name' => 'Report Admin',
            'email' => 'report-admin@example.test',
            'password' => 'secure-password-123',
            'role' => 'admin',
            'is_active' => true,
        ]);

        DB::table('admins')->insert(['user_id' => $user->id]);

        return $user;
    }

    public function test_item_code_is_generated_from_name_initials_and_database_id(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->postJson('/admin/items', [
            'name' => 'Chronos Sandglass',
            'unit_code' => 'CLIENT-SUPPLIED-CODE',
        ])->assertCreated();

        $itemId = $response->json('data.id');

        $this->assertNotNull($itemId);
        $this->assertSame('CS-'.$itemId, $response->json('data.unit_code'));
        $this->assertDatabaseHas('items', [
            'id' => $itemId,
            'name' => 'Chronos Sandglass',
            'unit_code' => 'CS-'.$itemId,
        ]);
    }

    public function test_admin_can_open_printable_pdf_report_page(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/reports/loans/print')
            ->assertOk()
            ->assertSee('Dream Pawn Shop — Laporan Pinjaman')
            ->assertSee('Cetak / Simpan sebagai PDF');
    }
}
