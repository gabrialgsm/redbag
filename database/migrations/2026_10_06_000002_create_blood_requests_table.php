<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('blood_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_code', 32)->unique();
            $table->string('requester_name');
            $table->string('requester_phone', 20);
            $table->string('patient_name')->nullable();
            $table->string('blood_group', 3);
            $table->unsignedTinyInteger('units')->default(1);
            $table->enum('urgency', ['normal','urgent','emergency'])->default('normal');
            $table->string('hospital_name');
            $table->string('area')->nullable();
            $table->string('district')->nullable();
            $table->timestamp('needed_at')->nullable();
            $table->enum('status', ['pending_verification','verified','matching','matched','completed','cancelled'])->default('pending_verification');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['blood_group','status','urgency']);
            $table->index(['district','area']);
        });
    }
    public function down(): void { Schema::dropIfExists('blood_requests'); }
};