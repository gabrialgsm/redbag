<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blood_request_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blood_request_id')->constrained('blood_requests')->restrictOnDelete();
            $table->string('actor_type', 30)->default('system');
            $table->string('actor_identifier', 190)->nullable();
            $table->string('event', 60);
            $table->string('from_status', 40)->nullable();
            $table->string('to_status', 40)->nullable();
            $table->text('reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['blood_request_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blood_request_events');
    }
};
