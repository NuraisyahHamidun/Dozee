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
            if (!Schema::hasColumn('association_rules', 'rule_text')) {
                $table->text('rule_text')->nullable()->after('rule_id');
            }
            $table->string('antecedent')->nullable()->change();
            $table->string('consequent')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('association_rules', function (Blueprint $table) {
            if (Schema::hasColumn('association_rules', 'rule_text')) {
                $table->dropColumn('rule_text');
            }
            $table->string('antecedent')->nullable(false)->change();
            $table->string('consequent')->nullable(false)->change();
        });
    }
};
