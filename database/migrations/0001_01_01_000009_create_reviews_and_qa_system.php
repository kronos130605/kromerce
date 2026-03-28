<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates product reviews and Q&A system tables.
     */
    public function up(): void
    {
        // Product reviews
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->uuid('product_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('order_id')->nullable(); // Verified purchase
            
            // Review content
            $table->string('title');
            $table->text('content');
            $table->integer('rating'); // 1-5 stars
            
            // Detailed ratings (optional)
            $table->integer('quality_rating')->nullable();
            $table->integer('value_rating')->nullable();
            $table->integer('service_rating')->nullable();
            
            // Verification
            $table->boolean('verified_purchase')->default(false);
            $table->boolean('is_anonymous')->default(false);
            
            // Moderation
            $table->string('status', 20)->default('pending'); // pending, approved, rejected, spam
            $table->foreignId('moderated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('moderated_at')->nullable();
            $table->text('moderation_notes')->nullable();
            
            // Helpfulness
            $table->integer('helpful_count')->default(0);
            $table->integer('not_helpful_count')->default(0);
            
            // Media
            $table->json('images')->nullable(); // Array of image URLs
            $table->json('videos')->nullable(); // Array of video URLs
            
            // Metadata
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->index(['product_id', 'status', 'created_at'], 'pr_product_status_date');
            $table->index(['store_id', 'status'], 'pr_store_status');
            $table->index(['user_id', 'product_id'], 'pr_user_product');
        });

        // Review helpfulness votes
        Schema::create('review_votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('review_id');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable(); // For guest users
            $table->boolean('is_helpful'); // true=helpful, false=not helpful
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            
            $table->foreign('review_id')->references('id')->on('product_reviews')->onDelete('cascade');
            $table->unique(['review_id', 'user_id'], 'rv_review_user_unique');
            $table->index(['review_id', 'is_helpful'], 'rv_review_helpful');
        });

        // Review responses (store replies to reviews)
        Schema::create('review_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('review_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Store staff
            $table->text('content');
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('review_id')->references('id')->on('product_reviews')->onDelete('cascade');
            $table->index(['review_id', 'created_at'], 'rr_review_date');
        });

        // Product questions
        Schema::create('product_questions', function (Blueprint $table) {
            $table->id();
            $table->uuid('product_id');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->unsignedBigInteger('store_id');
            
            // Question content
            $table->text('question');
            $table->boolean('is_anonymous')->default(false);
            
            // Moderation
            $table->string('status', 20)->default('pending'); // pending, approved, rejected, answered
            $table->foreignId('moderated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('moderated_at')->nullable();
            
            // Helpfulness
            $table->integer('helpful_count')->default(0);
            
            // Metadata
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->index(['product_id', 'status', 'created_at'], 'pq_product_status_date');
            $table->index(['store_id', 'status'], 'pq_store_status');
        });

        // Product answers
        Schema::create('product_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Answer content
            $table->text('answer');
            $table->boolean('is_official')->default(false); // From store staff
            $table->boolean('is_verified')->default(false); // Verified by store
            
            // Moderation
            $table->string('status', 20)->default('pending'); // pending, approved, rejected
            $table->foreignId('moderated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('moderated_at')->nullable();
            
            // Helpfulness
            $table->integer('helpful_count')->default(0);
            $table->integer('not_helpful_count')->default(0);
            
            // Metadata
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('question_id')->references('id')->on('product_questions')->onDelete('cascade');
            $table->index(['question_id', 'status', 'is_official'], 'pa_question_status_official');
            $table->index(['user_id', 'created_at'], 'pa_user_date');
        });

        // Answer helpfulness votes
        Schema::create('answer_votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('answer_id');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable();
            $table->boolean('is_helpful');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            
            $table->foreign('answer_id')->references('id')->on('product_answers')->onDelete('cascade');
            $table->unique(['answer_id', 'user_id'], 'av_answer_user_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answer_votes');
        Schema::dropIfExists('product_answers');
        Schema::dropIfExists('product_questions');
        Schema::dropIfExists('review_responses');
        Schema::dropIfExists('review_votes');
        Schema::dropIfExists('product_reviews');
    }
};
