<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('association_rules', function (Blueprint $table) {
            $table->id('rule_id');
            $table->string('antecedent');
            $table->string('consequent');
            $table->decimal('confidence', 5, 2);
            $table->decimal('lift', 5, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('association_rules');
    }
};
