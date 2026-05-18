<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddPaidAmountToPurchaseTransactionProductsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // 1. Create columns if they don't exist in purchase_transaction_products table
        Schema::table('purchase_transaction_products', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_transaction_products', 'paid_amount')) {
                $table->double('paid_amount')->default(0)->after('amount');
            }

            if (!Schema::hasColumn('purchase_transaction_products', 'desc_amount')) {
                $table->double('desc_amount')->default(0)->after('paid_amount');
            }
        });

        // Use raw MySQL statement to ensure identical double NOT NULL DEFAULT 0 types
        DB::statement("ALTER TABLE purchase_transaction_products MODIFY paid_amount DOUBLE NOT NULL DEFAULT 0;");
        DB::statement("ALTER TABLE purchase_transaction_products MODIFY amount DOUBLE NOT NULL DEFAULT 0;");
        DB::statement("ALTER TABLE purchase_transaction_products MODIFY desc_amount DOUBLE NOT NULL DEFAULT 0;");

        // 2. Create columns if they don't exist in purchase_transactions table
        Schema::table('purchase_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_transactions', 'desc_amount')) {
                $table->double('desc_amount')->default(0)->after('paid_amount');
            }
        });

        // Use raw MySQL statement to ensure identical double NOT NULL DEFAULT 0 types
        DB::statement("ALTER TABLE purchase_transactions MODIFY paid_amount DOUBLE NOT NULL DEFAULT 0;");
        DB::statement("ALTER TABLE purchase_transactions MODIFY amount DOUBLE NOT NULL DEFAULT 0;");
        DB::statement("ALTER TABLE purchase_transactions MODIFY net_amount DOUBLE NOT NULL DEFAULT 0;");
        DB::statement("ALTER TABLE purchase_transactions MODIFY desc_amount DOUBLE NOT NULL DEFAULT 0;");
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('purchase_transaction_products', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_transaction_products', 'desc_amount')) {
                $table->dropColumn('desc_amount');
            }

            if (Schema::hasColumn('purchase_transaction_products', 'paid_amount')) {
                $table->dropColumn('paid_amount');
            }
        });

        Schema::table('purchase_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_transactions', 'desc_amount')) {
                $table->dropColumn('desc_amount');
            }
        });

        // Revert paid_amount in purchase_transactions back to nullable double
        DB::statement("ALTER TABLE purchase_transactions MODIFY paid_amount DOUBLE NULL DEFAULT NULL;");
    }
}