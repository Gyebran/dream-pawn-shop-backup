<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('loan_request_item_id')->unique()->constrained('loan_request_items');
            $table->foreignId('recorded_by')->nullable()->constrained('admins', 'user_id');
            $table->foreignId('paid_confirmed_by')->nullable()->constrained('admins', 'user_id');
            $table->integer('amount');
            $table->string('status');
            $table->timestamp('assessed_at');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fines');
    }
};
