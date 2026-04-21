<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtpTable extends Migration
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
        if (!Schema::hasTable('otp_tables')) {
            Schema::create('otp_tables', function (Blueprint $table) {
                $table->id();
            });
        }

        Schema::table('otp_tables', function (Blueprint $table) {
            if (!Schema::hasColumn('otp_tables', 'mail')) {
                $table->string('mail')->nullable()->index();
            }
            if (!Schema::hasColumn('otp_tables', 'otp')) {
                $table->string('otp')->nullable();
            }
            if (!Schema::hasColumn('otp_tables', 'msg')) {
                $table->string('msg')->nullable();
            }
            if (!Schema::hasColumn('otp_tables', 'type')) {
                $table->string('type')->nullable();
            }
            if (!Schema::hasColumn('otp_tables', 'status')) {
                $table->integer('status')->nullable()->default(0);
            }
            if (!Schema::hasColumn('otp_tables', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('otp_tables', 'updated_at')) {
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
        if (Schema::hasTable('otp_tables')) {
            Schema::table('otp_tables', function (Blueprint $table) {
                if (Schema::hasColumn('otp_tables', 'mail')) {
                    $table->dropColumn('mail');
                }
                if (Schema::hasColumn('otp_tables', 'otp')) {
                    $table->dropColumn('otp');
                }
                if (Schema::hasColumn('otp_tables', 'msg')) {
                    $table->dropColumn('msg');
                }
                if (Schema::hasColumn('otp_tables', 'type')) {
                    $table->dropColumn('type');
                }
                if (Schema::hasColumn('otp_tables', 'status')) {
                    $table->dropColumn('status');
                }
                if (Schema::hasColumn('otp_tables', 'created_at')) {
                    $table->dropColumn('created_at');
                }
                if (Schema::hasColumn('otp_tables', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
            });
        }
    }
}
