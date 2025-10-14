<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('video')->nullable()->after('image');
            $table->enum('priority', ['low', 'medium', 'high', 'emergency'])->default('low')->after('location_name');
            $table->boolean('is_verified')->default(false)->after('priority');
            $table->integer('verification_score')->default(0)->after('is_verified');
            $table->integer('upvotes')->default(0)->after('verification_score');
            $table->integer('downvotes')->default(0)->after('upvotes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['video', 'priority', 'is_verified', 'verification_score', 'upvotes', 'downvotes']);
        });
    }
};
