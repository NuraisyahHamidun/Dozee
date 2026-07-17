<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales_transactions', function (Blueprint $table) {
            $table->string('status')->default('Pending')->after('total_amount');
            $table->timestamp('date_create')->nullable()->after('status');
            $table->timestamp('date_modifier')->nullable()->after('date_create');
            $table->timestamp('date_verify')->nullable()->after('date_modifier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_transactions', function (Blueprint $table) {
            $table->dropColumn(['status', 'date_create', 'date_modifier', 'date_verify']);
        });
    }
};
