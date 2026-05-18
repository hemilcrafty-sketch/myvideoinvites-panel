<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInternalUserColumnInUserData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_data', function (Blueprint $table) {
            if (!Schema::hasColumn('user_data', 'internal_user')) {
                $table->boolean('internal_user')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_data', function (Blueprint $table) {
            if (Schema::hasColumn('user_data', 'internal_user')) {
                $table->dropColumn('internal_user');
            }
        });
    }
}