<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the domains table for store-based multi-tenancy.
     */
    public function up(): void
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->string('domain')->unique(); // ejemplo.com, www.ejemplo.com
            $table->string('type', 20)->default('primary'); // primary, secondary, redirect, www
            $table->boolean('is_active')->default(true);
            $table->boolean('is_https')->default(true);
            $table->string('ssl_certificate')->nullable(); // Path or ID of SSL cert
            $table->timestamp('ssl_expires_at')->nullable();
            $table->json('dns_config')->nullable(); // DNS configuration details
            $table->json('redirect_config')->nullable(); // For redirect type domains
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->index(['store_id', 'is_active']);
            $table->index(['domain', 'is_active']);
        });

        // Domain verification history
        Schema::create('domain_verifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('domain_id');
            $table->string('method'); // dns, file, email, manual
            $table->string('status'); // pending, verified, failed, expired
            $table->text('verification_data'); // Token, TXT record, or file content
            $table->timestamp('attempted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();
            
            $table->foreign('domain_id')->references('id')->on('domains')->onDelete('cascade');
            $table->index(['domain_id', 'status']);
        });

        // Domain SSL certificate history
        Schema::create('domain_ssl_certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('domain_id');
            $table->string('provider'); // letsencrypt, custom, cloudflare
            $table->text('certificate');
            $table->text('private_key');
            $table->timestamp('issued_at');
            $table->timestamp('expires_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('domain_id')->references('id')->on('domains')->onDelete('cascade');
            $table->index(['domain_id', 'is_active']);
        });

        // Domain redirect rules
        Schema::create('domain_redirects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('domain_id');
            $table->string('source_path'); // /old-page, /products/*
            $table->string('target_url'); // https://newsite.com/new-page
            $table->integer('status_code')->default(301); // 301, 302, 307, 308
            $table->boolean('is_regex')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('domain_id')->references('id')->on('domains')->onDelete('cascade');
            $table->index(['domain_id', 'is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_redirects');
        Schema::dropIfExists('domain_ssl_certificates');
        Schema::dropIfExists('domain_verifications');
        Schema::dropIfExists('domains');
    }
};
