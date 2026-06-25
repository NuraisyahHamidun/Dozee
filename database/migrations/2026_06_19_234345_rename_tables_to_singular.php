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
        // 1. Drop existing foreign keys
        Schema::table('promotion_association_rule', function (Blueprint $table) {
            $table->dropForeign(['promotion_id']);
        });

        Schema::table('transaction_details', function (Blueprint $table) {
            $table->dropForeign(['transaction_id']);
            $table->dropForeign(['item_id']);
            $table->dropForeign(['promo_id']);
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->dropForeign(['manager_id']);
            $table->dropForeign(['salesman_id']);
        });

        Schema::table('sales_transactions', function (Blueprint $table) {
            $table->dropForeign(['salesman_id']);
        });

        Schema::table('salesmen', function (Blueprint $table) {
            $table->dropForeign(['manager_id']);
        });

        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });

        // 2. Rename tables from plural to singular
        Schema::rename('items', 'item');
        Schema::rename('categories', 'category');
        Schema::rename('managers', 'manager');
        Schema::rename('salesmen', 'salesman');
        Schema::rename('promotions', 'promotion');
        Schema::rename('sales_transactions', 'sales_transaction');
        Schema::rename('transaction_details', 'transaction_detail');

        // 3. Re-create foreign keys pointing to singular tables
        Schema::table('salesman', function (Blueprint $table) {
            $table->foreign('manager_id')->references('manager_id')->on('manager')->cascadeOnDelete();
        });

        Schema::table('item', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('category')->onDelete('set null');
        });

        Schema::table('sales_transaction', function (Blueprint $table) {
            $table->foreign('salesman_id')->references('salesman_id')->on('salesman')->cascadeOnDelete();
        });

        Schema::table('promotion', function (Blueprint $table) {
            $table->foreign('manager_id')->references('manager_id')->on('manager')->cascadeOnDelete();
            $table->foreign('salesman_id')->references('salesman_id')->on('salesman')->nullOnDelete();
        });

        Schema::table('transaction_detail', function (Blueprint $table) {
            $table->foreign('transaction_id')->references('transaction_id')->on('sales_transaction')->cascadeOnDelete();
            $table->foreign('item_id')->references('item_id')->on('item')->cascadeOnDelete();
            $table->foreign('promo_id')->references('promo_id')->on('promotion')->nullOnDelete();
        });

        Schema::table('promotion_association_rule', function (Blueprint $table) {
            $table->foreign('promotion_id')->references('promo_id')->on('promotion')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Drop singular foreign keys
        Schema::table('promotion_association_rule', function (Blueprint $table) {
            $table->dropForeign(['promotion_id']);
        });

        Schema::table('transaction_detail', function (Blueprint $table) {
            $table->dropForeign(['transaction_id']);
            $table->dropForeign(['item_id']);
            $table->dropForeign(['promo_id']);
        });

        Schema::table('promotion', function (Blueprint $table) {
            $table->dropForeign(['manager_id']);
            $table->dropForeign(['salesman_id']);
        });

        Schema::table('sales_transaction', function (Blueprint $table) {
            $table->dropForeign(['salesman_id']);
        });

        Schema::table('salesman', function (Blueprint $table) {
            $table->dropForeign(['manager_id']);
        });

        Schema::table('item', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });

        // 2. Rename tables from singular back to plural
        Schema::rename('item', 'items');
        Schema::rename('category', 'categories');
        Schema::rename('manager', 'managers');
        Schema::rename('salesman', 'salesmen');
        Schema::rename('promotion', 'promotions');
        Schema::rename('sales_transaction', 'sales_transactions');
        Schema::rename('transaction_detail', 'transaction_details');

        // 3. Re-create plural foreign keys
        Schema::table('salesmen', function (Blueprint $table) {
            $table->foreign('manager_id')->references('manager_id')->on('managers')->cascadeOnDelete();
        });

        Schema::table('items', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });

        Schema::table('sales_transactions', function (Blueprint $table) {
            $table->foreign('salesman_id')->references('salesman_id')->on('salesmen')->cascadeOnDelete();
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->foreign('manager_id')->references('manager_id')->on('managers')->cascadeOnDelete();
            $table->foreign('salesman_id')->references('salesman_id')->on('salesmen')->nullOnDelete();
        });

        Schema::table('transaction_details', function (Blueprint $table) {
            $table->foreign('transaction_id')->references('transaction_id')->on('sales_transactions')->cascadeOnDelete();
            $table->foreign('item_id')->references('item_id')->on('items')->cascadeOnDelete();
            $table->foreign('promo_id')->references('promo_id')->on('promotions')->nullOnDelete();
        });

        Schema::table('promotion_association_rule', function (Blueprint $table) {
            $table->foreign('promotion_id')->references('promo_id')->on('promotions')->cascadeOnDelete();
        });
    }
};
