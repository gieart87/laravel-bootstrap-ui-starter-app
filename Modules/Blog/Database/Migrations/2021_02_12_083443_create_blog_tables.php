<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlogTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->index();
            $table->string('post_type')->index();
            $table->string('code')->unique();
            $table->string('title');
            $table->string('slug')->unique();
            $table->integer('status')->default(0);
            $table->dateTime('publish_date')->index()->nullable();
            $table->text('excerpt')->nullable();
            $table->text('body')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('featured_image_caption')->nullable();
            $table->json('metas')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('blog_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('parent_id')->index()->nullable();
            $table->string('slug');
            $table->unique(['slug', 'parent_id']);
            $table->string('name');
            $table->timestamps();

            $table->index('created_at');
        });

        Schema::create('blog_categories_posts', function (Blueprint $table) {
            $table->uuid('post_id');
            $table->uuid('category_id');

            $table->unique(['post_id', 'category_id']);
            $table->foreign('post_id')->references('id')->on('blog_posts');
            $table->foreign('category_id')->references('id')->on('blog_categories');
        });

        Schema::create('blog_tags', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug')->unique();
            $table->string('name');
            $table->timestamps();

            $table->index('created_at');
        });

        Schema::create('blog_posts_tags', function (Blueprint $table) {
            $table->uuid('post_id');
            $table->uuid('tag_id');

            $table->unique(['post_id', 'tag_id']);
            $table->foreign('post_id')->references('id')->on('blog_posts');
            $table->foreign('tag_id')->references('id')->on('blog_tags');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blog_categories_posts');
        Schema::dropIfExists('blog_categories');
        Schema::dropIfExists('blog_posts_tags');
        Schema::dropIfExists('blog_tags');
        Schema::dropIfExists('blog_posts');
    }
}
