<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * The database connection that should be used by the migration.
     *
     * @var string
     */
    protected $connection = 'crafty_db_mysql';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
            });
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'name')) {
                $table->string('name');
            }
            if (!Schema::hasColumn('users', 'email')) {
                $table->string('email')->unique();
            }
            if (!Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'mobile_number')) {
                $table->string('mobile_number')->nullable();
            }
            if (!Schema::hasColumn('users', 'meta_api_token')) {
                $table->string('meta_api_token')->nullable();
            }
            if (!Schema::hasColumn('users', 'password')) {
                $table->string('password');
            }
            if (!Schema::hasColumn('users', 'user_type')) {
                $table->integer('user_type')->default(0);
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->integer('status')->default(1);
            }
            if (!Schema::hasColumn('users', 'team_leader_id')) {
                $table->integer('team_leader_id')->nullable()->index();
            }
            if (!Schema::hasColumn('users', 'remember_token')) {
                $table->rememberToken();
            }
            if (!Schema::hasColumn('users', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'updated_at')) {
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
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'name')) {
                    $table->dropColumn('name');
                }
                if (Schema::hasColumn('users', 'email')) {
                    $table->dropColumn('email');
                }
                if (Schema::hasColumn('users', 'email_verified_at')) {
                    $table->dropColumn('email_verified_at');
                }
                if (Schema::hasColumn('users', 'mobile_number')) {
                    $table->dropColumn('mobile_number');
                }
                if (Schema::hasColumn('users', 'meta_api_token')) {
                    $table->dropColumn('meta_api_token');
                }
                if (Schema::hasColumn('users', 'password')) {
                    $table->dropColumn('password');
                }
                if (Schema::hasColumn('users', 'user_type')) {
                    $table->dropColumn('user_type');
                }
                if (Schema::hasColumn('users', 'status')) {
                    $table->dropColumn('status');
                }
                if (Schema::hasColumn('users', 'team_leader_id')) {
                    $table->dropColumn('team_leader_id');
                }
                if (Schema::hasColumn('users', 'remember_token')) {
                    $table->dropColumn('remember_token');
                }
                if (Schema::hasColumn('users', 'created_at')) {
                    $table->dropColumn('created_at');
                }
                if (Schema::hasColumn('users', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
            });
        }
    }
}
