<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVirtualCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('virtual_categories')) {
            Schema::create('virtual_categories', function (Blueprint $table) {
                $table->id();
                $table->integer('parent_category_id')->nullable()->index();
                $table->string('slug')->nullable()->unique();
                $table->string('string_id')->nullable()->index();
                $table->integer('emp_id')->nullable();
                $table->string('seo_emp_id')->nullable();
                $table->string('canonical_link')->nullable();
                $table->string('meta_title')->nullable();
                $table->text('meta_desc')->nullable();
                $table->string('h1_tag')->nullable();
                $table->string('h2_tag')->nullable();
                $table->text('short_desc')->nullable();
                $table->text('long_desc')->nullable();
                $table->string('tag_line')->nullable();
                $table->string('category_name');
                $table->string('size')->nullable();
                $table->string('category_thumb')->nullable();
                $table->string('mockup')->nullable();
                $table->string('banner')->nullable();
                $table->text('contents')->nullable();
                $table->text('faqs')->nullable();
                $table->string('fldr_str')->nullable();
                $table->text('top_keywords')->nullable();
                $table->string('cta')->nullable();
                $table->text('primary_keyword')->nullable();
                $table->integer('imp')->default(0);
                $table->integer('sequence_number')->default(0);
                $table->boolean('no_index')->default(false);
                $table->string('priority')->nullable();
                $table->string('frequency')->nullable();
                $table->integer('status')->default(1);
                $table->boolean('deleted')->default(false);
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
        if (Schema::hasTable('virtual_categories')) {
            Schema::dropIfExists('virtual_categories');
        }
    }
}
