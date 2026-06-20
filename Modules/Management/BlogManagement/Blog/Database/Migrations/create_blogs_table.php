<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     php artisan migrate --path='/app/Modules/Management/BlogManagement/Blog/Database/create_blogs_table.php'
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('blog_category_id')->nullable();
            $table->bigInteger('writer_id')->nullable();
            $table->string('title', 200)->nullable();
            $table->text('short_description')->nullable();
            $table->longtext('content')->nullable();
            $table->integer('reading_time')->nullable();
            $table->decimal('average_rating')->nullable();
            $table->date('publish_date')->nullable();
            $table->datetime('scheduled_at')->nullable();
            $table->string('thumbnail_image', 150)->nullable();
            $table->text('gallery')->nullable();
            $table->enum('blog_type', ['news','tutorial','opinion','review','case_study'])->nullable();
            $table->enum('content_format', ['article','video','podcast','infographic'])->nullable();
            $table->string('external_url', 100)->nullable();
            $table->enum('show_on_top', ['yes','no'])->nullable();
            $table->enum('allow_comments', ['yes','no'])->nullable();
            $table->tinyInteger('is_featured')->default(0);
            $table->tinyInteger('is_published')->default(0);
            $table->string('video_link', 200)->nullable();
            $table->string('meta_title', 200)->nullable();
            $table->text('meta_description')->nullable();
            $table->json('meta_keywords')->nullable();

            $table->bigInteger('creator')->unsigned()->nullable();
            $table->string('slug', 50)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};