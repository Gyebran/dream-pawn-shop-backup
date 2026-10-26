<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_request_items', function (Blueprint $table): void {
            $table->unique(['loan_request_id', 'item_id']);
            $table->index('item_id');
        });

        Schema::table('loan_requests', function (Blueprint $table): void {
            $table->index(['member_id', 'status']);
            $table->index(['status', 'requested_at']);
        });

        Schema::table('items', function (Blueprint $table): void {
            $table->index(['status', 'name']);
        });

        Schema::table('fines', function (Blueprint $table): void {
            $table->index('status');
        });

        Schema::table('item_category', function (Blueprint $table): void {
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::table('item_category', function (Blueprint $table): void {
            $table->dropIndex(['category_id']);
        });

        Schema::table('fines', function (Blueprint $table): void {
            $table->dropIndex(['status']);
        });

        Schema::table('items', function (Blueprint $table): void {
            $table->dropIndex(['status', 'name']);
        });

        Schema::table('loan_requests', function (Blueprint $table): void {
            $table->dropIndex(['member_id', 'status']);
            $table->dropIndex(['status', 'requested_at']);
        });

        Schema::table('loan_request_items', function (Blueprint $table): void {
            $table->dropIndex(['item_id']);
            $table->dropUnique(['loan_request_id', 'item_id']);
        });
    }
};
