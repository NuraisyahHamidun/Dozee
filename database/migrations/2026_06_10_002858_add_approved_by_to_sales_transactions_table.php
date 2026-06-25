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
            $table->unsignedBigInteger('approved_by')->nullable()->after('date_verify');
            
            // Optionally set up foreign key if 'managers' table exists with 'manager_id'
            // $table->foreign('approved_by')->references('manager_id')->on('managers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_transactions', function (Blueprint $table) {
            $table->dropColumn('approved_by');
        });
    }
};
