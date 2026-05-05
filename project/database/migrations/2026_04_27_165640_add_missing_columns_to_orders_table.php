<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'contact_no')) {
                $table->string('contact_no')->nullable()->after('crafty_id');
            }
            if (!Schema::hasColumn('orders', 'has_offer')) {
                $table->boolean('has_offer')->default(0)->after('type');
            }
            if (!Schema::hasColumn('orders', 'show_data')) {
                $table->boolean('show_data')->default(1)->after('has_offer');
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['contact_no', 'has_offer', 'show_data']);
        });
    }
}
