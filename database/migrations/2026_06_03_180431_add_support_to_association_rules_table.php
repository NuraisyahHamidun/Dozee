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
        Schema::table('association_rules', function (Blueprint $table) {
            // Add support column if it doesn't exist
            if (!Schema::hasColumn('association_rules', 'support')) {
                $table->decimal('support', 6, 4)->default(0)->after('consequent');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('association_rules', function (Blueprint $table) {
            $table->dropColumn('support');
        });
    }
};
