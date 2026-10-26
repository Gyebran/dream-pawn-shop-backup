<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('member_id')->constrained('members', 'user_id');
            $table->foreignId('reviewed_by')->nullable()->constrained('admins', 'user_id');
            $table->foreignId('return_confirmed_by')->nullable()->constrained('admins', 'user_id');
            $table->string('status');
            $table->timestamp('requested_at');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('handover_deadline_at')->nullable();
            $table->timestamp('handed_over_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_requests');
    }
};
