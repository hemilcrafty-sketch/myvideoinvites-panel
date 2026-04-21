<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDataDeletedTable extends Migration
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
        if (!Schema::hasTable('user_data_deleted')) {
            Schema::create('user_data_deleted', function (Blueprint $table) {
                $table->id();
            });
        }

        Schema::table('user_data_deleted', function (Blueprint $table) {
            if (!Schema::hasColumn('user_data_deleted', 'user_int_id')) {
                $table->integer('user_int_id')->index();
            }
            if (!Schema::hasColumn('user_data_deleted', 'uid')) {
                $table->string('uid')->index();
            }
            if (!Schema::hasColumn('user_data_deleted', 'refer_id')) {
                $table->string('refer_id')->nullable();
            }
            if (!Schema::hasColumn('user_data_deleted', 'stripe_cus_id')) {
                $table->string('stripe_cus_id')->nullable();
            }
            if (!Schema::hasColumn('user_data_deleted', 'razorpay_cus_id')) {
                $table->string('razorpay_cus_id')->nullable();
            }
            if (!Schema::hasColumn('user_data_deleted', 'photo_uri')) {
                $table->string('photo_uri')->nullable();
            }
            if (!Schema::hasColumn('user_data_deleted', 'name')) {
                $table->string('name');
            }
            if (!Schema::hasColumn('user_data_deleted', 'country_code')) {
                $table->string('country_code')->nullable();
            }
            if (!Schema::hasColumn('user_data_deleted', 'number')) {
                $table->string('number')->nullable();
            }
            if (!Schema::hasColumn('user_data_deleted', 'email')) {
                $table->string('email')->nullable();
            }
            if (!Schema::hasColumn('user_data_deleted', 'login_type')) {
                $table->string('login_type')->nullable();
            }
            if (!Schema::hasColumn('user_data_deleted', 'utm_source')) {
                $table->string('utm_source')->nullable();
            }
            if (!Schema::hasColumn('user_data_deleted', 'utm_medium')) {
                $table->string('utm_medium')->nullable();
            }
            if (!Schema::hasColumn('user_data_deleted', 'coins')) {
                $table->integer('coins')->default(0);
            }
            if (!Schema::hasColumn('user_data_deleted', 'device_id')) {
                $table->string('device_id')->nullable();
            }
            if (!Schema::hasColumn('user_data_deleted', 'fldr_str')) {
                $table->string('fldr_str')->nullable();
            }
            if (!Schema::hasColumn('user_data_deleted', 'referral_user_id')) {
                $table->string('referral_user_id')->nullable();
            }
            if (!Schema::hasColumn('user_data_deleted', 'creation_date')) {
                $table->string('creation_date')->nullable();
            }
            if (!Schema::hasColumn('user_data_deleted', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('user_data_deleted', 'updated_at')) {
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
        if (Schema::hasTable('user_data_deleted')) {
            Schema::table('user_data_deleted', function (Blueprint $table) {
                if (Schema::hasColumn('user_data_deleted', 'user_int_id')) {
                    $table->dropColumn('user_int_id');
                }
                if (Schema::hasColumn('user_data_deleted', 'uid')) {
                    $table->dropColumn('uid');
                }
                if (Schema::hasColumn('user_data_deleted', 'refer_id')) {
                    $table->dropColumn('refer_id');
                }
                if (Schema::hasColumn('user_data_deleted', 'stripe_cus_id')) {
                    $table->dropColumn('stripe_cus_id');
                }
                if (Schema::hasColumn('user_data_deleted', 'razorpay_cus_id')) {
                    $table->dropColumn('razorpay_cus_id');
                }
                if (Schema::hasColumn('user_data_deleted', 'photo_uri')) {
                    $table->dropColumn('photo_uri');
                }
                if (Schema::hasColumn('user_data_deleted', 'name')) {
                    $table->dropColumn('name');
                }
                if (Schema::hasColumn('user_data_deleted', 'country_code')) {
                    $table->dropColumn('country_code');
                }
                if (Schema::hasColumn('user_data_deleted', 'number')) {
                    $table->dropColumn('number');
                }
                if (Schema::hasColumn('user_data_deleted', 'email')) {
                    $table->dropColumn('email');
                }
                if (Schema::hasColumn('user_data_deleted', 'login_type')) {
                    $table->dropColumn('login_type');
                }
                if (Schema::hasColumn('user_data_deleted', 'utm_source')) {
                    $table->dropColumn('utm_source');
                }
                if (Schema::hasColumn('user_data_deleted', 'utm_medium')) {
                    $table->dropColumn('utm_medium');
                }
                if (Schema::hasColumn('user_data_deleted', 'coins')) {
                    $table->dropColumn('coins');
                }
                if (Schema::hasColumn('user_data_deleted', 'device_id')) {
                    $table->dropColumn('device_id');
                }
                if (Schema::hasColumn('user_data_deleted', 'fldr_str')) {
                    $table->dropColumn('fldr_str');
                }
                if (Schema::hasColumn('user_data_deleted', 'referral_user_id')) {
                    $table->dropColumn('referral_user_id');
                }
                if (Schema::hasColumn('user_data_deleted', 'creation_date')) {
                    $table->dropColumn('creation_date');
                }
                if (Schema::hasColumn('user_data_deleted', 'created_at')) {
                    $table->dropColumn('created_at');
                }
                if (Schema::hasColumn('user_data_deleted', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
            });
        }
    }
}
