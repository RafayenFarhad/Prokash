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
        Schema::create('community_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('response_type'); // 'help', 'resource', 'info', 'volunteer'
            $table->text('message');
            $table->json('resources')->nullable(); // blood_type, vehicle, skills, etc.
            $table->string('contact_method')->nullable(); // phone, email, whatsapp
            $table->string('contact_info')->nullable();
            $table->string('status')->default('active'); // active, fulfilled, cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_responses');
    }
};
