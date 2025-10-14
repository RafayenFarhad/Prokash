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
            $table->boolean('admin_verified')->default(false)->after('is_verified');
            $table->unsignedBigInteger('admin_verified_by')->nullable()->after('admin_verified');
            $table->timestamp('admin_verified_at')->nullable()->after('admin_verified_by');
            
            $table->foreign('admin_verified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['admin_verified_by']);
            $table->dropColumn(['admin_verified', 'admin_verified_by', 'admin_verified_at']);
        });
    }
};
