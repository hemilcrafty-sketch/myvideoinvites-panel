<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('items')) {
            Schema::create('items', function (Blueprint $table) {
                $table->id();
                $table->string('string_id')->nullable()->index();
                $table->string('video_name');
                $table->string('slug')->unique();
                $table->string('video_url')->nullable();
                $table->string('video_thumb')->nullable();
                $table->string('video_zip_url')->nullable();
                $table->string('folder_name')->nullable();
                $table->integer('category_id')->index();
                $table->integer('virtual_category_id')->nullable()->index();
                $table->integer('template_type')->default(0);
                $table->string('template_size')->nullable();
                $table->integer('width')->default(0);
                $table->integer('height')->default(0);
                $table->integer('pages')->default(1);
                $table->string('orientation')->nullable();
                $table->integer('lang_id')->nullable();
                $table->integer('religion_id')->nullable();
                $table->integer('interest_id')->nullable();
                $table->integer('style_id')->nullable();
                $table->integer('theme_id')->nullable();
                $table->string('color_ids')->nullable();
                $table->integer('relation_id')->nullable();
                $table->boolean('is_premium')->default(false);
                $table->boolean('is_freemium')->default(false);
                $table->integer('status')->default(1);
                $table->integer('priority')->default(0);
                $table->integer('views')->default(0);
                $table->integer('daily_views')->default(0);
                $table->integer('weekly_views')->default(0);
                $table->integer('creation')->default(0);
                $table->integer('daily_creation')->default(0);
                $table->integer('weekly_creation')->default(0);
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->string('keyword')->nullable();
                $table->string('h2_tag')->nullable();
                $table->string('canonical_link')->nullable();
                $table->boolean('no_index')->default(false);
                $table->boolean('editable_text')->default(true);
                $table->boolean('editable_image')->default(true);
                $table->boolean('change_music')->default(true);
                $table->boolean('change_text')->default(true);
                $table->boolean('do_front_lottie')->default(false);
                $table->integer('watermark_height')->nullable();
                $table->dateTime('start_date')->nullable();
                $table->dateTime('end_date')->nullable();
                $table->integer('emp_id')->nullable();
                $table->integer('seo_emp_id')->nullable();
                $table->integer('seo_assigner_id')->nullable();
                $table->boolean('encrypted')->default(false);
                $table->string('encryption_key')->nullable();
                $table->boolean('is_deleted')->default(false);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('items')) {
            Schema::dropIfExists('items');
        }
    }
}
