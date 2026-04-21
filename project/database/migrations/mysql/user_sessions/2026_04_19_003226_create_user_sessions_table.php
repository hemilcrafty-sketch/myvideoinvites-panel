<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSessionsTable extends Migration
{
    /**
     * The database connection that should be used by the migration.
     *
     * @var string
     */
    protected $connection = 'mysql';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('user_sessions')) {
            Schema::create('user_sessions', function (Blueprint $table) {
                $table->id();
            });
        }

        Schema::table('user_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('user_sessions', 'user_id')) {
                $table->string('user_id')->nullable()->index();
            }
            if (!Schema::hasColumn('user_sessions', 'device_id')) {
                $table->string('device_id')->index();
            }
            if (!Schema::hasColumn('user_sessions', 'token_id')) {
                $table->integer('token_id')->nullable();
            }
            if (!Schema::hasColumn('user_sessions', 'custom_token')) {
                $table->string('custom_token')->nullable();
            }
            if (!Schema::hasColumn('user_sessions', 'ip_address')) {
                $table->string('ip_address')->nullable();
            }
            if (!Schema::hasColumn('user_sessions', 'user_agent')) {
                $table->string('user_agent')->nullable();
            }
            if (!Schema::hasColumn('user_sessions', 'last_active')) {
                $table->timestamp('last_active')->nullable();
            }
            if (!Schema::hasColumn('user_sessions', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('user_sessions', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
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
        if (Schema::hasTable('user_sessions')) {
            Schema::table('user_sessions', function (Blueprint $table) {
                if (Schema::hasColumn('user_sessions', 'user_id')) {
                    $table->dropColumn('user_id');
                }
                if (Schema::hasColumn('user_sessions', 'device_id')) {
                    $table->dropColumn('device_id');
                }
                if (Schema::hasColumn('user_sessions', 'token_id')) {
                    $table->dropColumn('token_id');
                }
                if (Schema::hasColumn('user_sessions', 'custom_token')) {
                    $table->dropColumn('custom_token');
                }
                if (Schema::hasColumn('user_sessions', 'ip_address')) {
                    $table->dropColumn('ip_address');
                }
                if (Schema::hasColumn('user_sessions', 'user_agent')) {
                    $table->dropColumn('user_agent');
                }
                if (Schema::hasColumn('user_sessions', 'last_active')) {
                    $table->dropColumn('last_active');
                }
                if (Schema::hasColumn('user_sessions', 'created_at')) {
                    $table->dropColumn('created_at');
                }
                if (Schema::hasColumn('user_sessions', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
            });
        }
    }
}
