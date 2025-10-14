<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->boolean('phone_verified')->default(false)->after('phone');
            $table->string('phone_verification_code')->nullable()->after('phone_verified');
            $table->timestamp('phone_verified_at')->nullable()->after('phone_verification_code');
            $table->string('avatar')->nullable()->after('phone_verified_at');
            $table->text('bio')->nullable()->after('avatar');
            $table->decimal('latitude', 10, 8)->nullable()->after('bio');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('location_name')->nullable()->after('longitude');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'phone_verified', 'phone_verification_code', 
                'phone_verified_at', 'avatar', 'bio', 'latitude', 
                'longitude', 'location_name'
            ]);
        });
    }
};
