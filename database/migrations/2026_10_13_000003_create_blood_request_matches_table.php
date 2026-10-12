<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blood_request_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blood_request_id')->constrained('blood_requests')->restrictOnDelete();
            $table->foreignId('donor_id')->constrained('donors')->restrictOnDelete();
            $table->enum('response', ['pending', 'accepted', 'declined', 'maybe'])->default('pending');
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('contact_consent_at')->nullable();
            $table->timestamps();

            $table->unique(['blood_request_id', 'donor_id']);
            $table->index(['donor_id', 'response']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blood_request_matches');
    }
};
