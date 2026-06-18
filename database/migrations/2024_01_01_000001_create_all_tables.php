<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('email')->unique();
            $t->timestamp('email_verified_at')->nullable();
            $t->string('password');
            $t->enum('role', ['admin','editor'])->default('editor');
            $t->string('avatar')->nullable();
            $t->string('bio')->nullable();
            $t->rememberToken();
            $t->timestamp('last_login_at')->nullable();
            $t->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $t) {
            $t->string('email')->primary();
            $t->string('token');
            $t->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->foreignId('user_id')->nullable()->index();
            $t->string('ip_address', 45)->nullable();
            $t->text('user_agent')->nullable();
            $t->longText('payload');
            $t->integer('last_activity')->index();
        });

        Schema::create('categories', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->string('color')->default('#D4420A');
            $t->text('description')->nullable();
            $t->unsignedInteger('article_count')->default(0);
            $t->timestamps();
        });

        Schema::create('articles', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->string('slug')->unique();
            $t->text('excerpt')->nullable();
            $t->longText('body')->nullable();
            $t->string('cover_image')->nullable();
            $t->string('cover_emoji')->default('📰');
            $t->string('cover_bg')->default('linear-gradient(145deg,#1A1410,#221808)');
            $t->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $t->enum('status', ['draft','published'])->default('draft');
            $t->boolean('featured')->default(false);
            $t->boolean('breaking')->default(false);
            $t->unsignedBigInteger('views')->default(0);
            $t->string('read_time')->default('3 min');
            $t->json('tags')->nullable();
            $t->string('meta_title')->nullable();
            $t->text('meta_desc')->nullable();
            $t->timestamp('published_at')->nullable();
            $t->timestamps();
            $t->index(['status','published_at']);
        });

        Schema::create('media', function (Blueprint $t) {
            $t->id();
            $t->string('filename');
            $t->string('original_name');
            $t->string('mimetype');
            $t->unsignedBigInteger('size')->default(0);
            $t->string('url');
            $t->string('disk')->default('public');
            $t->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $t->timestamps();
        });

        Schema::create('settings', function (Blueprint $t) {
            $t->string('key')->primary();
            $t->text('value')->nullable();
            $t->string('type')->default('text');
            $t->string('group')->default('general');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('media');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
