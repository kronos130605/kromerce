<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates media library for centralized file management.
     */
    public function up(): void
    {
        // Media library
        Schema::create('media_library', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id')->nullable(); // Null for global media
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            // File information
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type');
            $table->string('disk')->default('public'); // public, s3, etc.
            $table->string('path');
            $table->string('url');
            $table->unsignedBigInteger('size'); // In bytes
            
            // File type
            $table->string('type', 20); // image, video, document, audio, other
            $table->string('extension', 10);
            
            // Image-specific
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('aspect_ratio')->nullable();
            
            // Organization
            $table->unsignedBigInteger('folder_id')->nullable();
            $table->json('tags')->nullable();
            $table->text('description')->nullable();
            $table->string('alt_text')->nullable();
            $table->string('caption')->nullable();
            
            // Thumbnails and variants
            $table->json('thumbnails')->nullable(); // Generated thumbnail URLs
            $table->json('responsive_images')->nullable(); // Different sizes
            
            // Usage tracking
            $table->integer('usage_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            
            // SEO
            $table->string('title')->nullable();
            
            // Metadata
            $table->json('metadata')->nullable(); // EXIF, camera info, etc.
            $table->string('hash')->nullable(); // File hash for duplicate detection
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->index(['store_id', 'type'], 'ml_store_type');
            $table->index(['store_id', 'folder_id'], 'ml_store_folder');
            $table->index('hash');
            $table->index('mime_type');
        });

        // Media folders
        Schema::create('media_folders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('color', 7)->nullable(); // Hex color for UI
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('media_folders')->onDelete('cascade');
            $table->index(['store_id', 'parent_id'], 'mf_store_parent');
        });

        // Add folder_id foreign key to media_library
        Schema::table('media_library', function (Blueprint $table) {
            $table->foreign('folder_id')->references('id')->on('media_folders')->onDelete('set null');
        });

        // Media usage tracking (polymorphic)
        Schema::create('media_usage', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('media_id');
            $table->string('usable_type'); // Product, Post, Page, etc.
            $table->string('usable_id');
            $table->string('context')->nullable(); // 'featured_image', 'gallery', 'content'
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->foreign('media_id')->references('id')->on('media_library')->onDelete('cascade');
            $table->index(['usable_type', 'usable_id'], 'mu_usable');
            $table->index(['media_id', 'usable_type'], 'mu_media_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_usage');
        
        Schema::table('media_library', function (Blueprint $table) {
            $table->dropForeign(['folder_id']);
        });
        
        Schema::dropIfExists('media_library');
        Schema::dropIfExists('media_folders');
    }
};
