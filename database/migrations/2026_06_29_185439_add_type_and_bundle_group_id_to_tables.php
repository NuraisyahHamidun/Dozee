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
        Schema::table('sales_transaction', function (Blueprint $table) {
            $table->enum('type', ['single', 'bundle', 'mixed'])->default('single')->after('total_amount');
        });

        Schema::table('transaction_detail', function (Blueprint $table) {
            $table->string('bundle_group_id')->nullable()->after('promo_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_transaction', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('transaction_detail', function (Blueprint $table) {
            $table->dropColumn('bundle_group_id');
        });
    }
};
