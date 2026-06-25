<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            DB::statement("ALTER TABLE promotions MODIFY COLUMN status ENUM('Pending', 'Active', 'Expired', 'Rejected') DEFAULT 'Pending'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            DB::statement("ALTER TABLE promotions MODIFY COLUMN status ENUM('Active', 'Expired') DEFAULT 'Active'");
        });
    }
};
