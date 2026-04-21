<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMainCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('main_categories')) {
            Schema::create('main_categories', function (Blueprint $table) {
                $table->id();
                $table->string('string_id')->nullable()->index();
                $table->string('category_name');
                $table->string('category_title')->nullable();
                $table->string('slug')->unique();
                $table->string('tag_line')->nullable();
                $table->text('short_desc')->nullable();
                $table->text('long_desc')->nullable();
                $table->string('category_thumb')->nullable();
                $table->string('banner')->nullable();
                $table->string('mockup')->nullable();
                $table->integer('parent_category_id')->default(0)->index();
                $table->string('child_cat_ids')->nullable();
                $table->integer('sequence_number')->default(0);
                $table->integer('priority')->default(0);
                $table->integer('status')->default(1);
                $table->integer('total_templates')->default(0);
                $table->string('meta_title')->nullable();
                $table->text('meta_desc')->nullable();
                $table->string('h1_tag')->nullable();
                $table->string('h2_tag')->nullable();
                $table->text('primary_keyword')->nullable();
                $table->text('top_keywords')->nullable();
                $table->string('canonical_link')->nullable();
                $table->boolean('no_index')->default(false);
                $table->text('contents')->nullable();
                $table->text('faqs')->nullable();
                $table->integer('emp_id')->nullable();
                $table->integer('seo_emp_id')->nullable();
                $table->string('fldr_str')->nullable();
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
        if (Schema::hasTable('main_categories')) {
            Schema::dropIfExists('main_categories');
        }
    }
}
