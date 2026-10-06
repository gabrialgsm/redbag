<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('donors', function (Blueprint $table) {
            $table->id();
            $table->string('donor_code', 32)->unique();
            $table->string('name');
            $table->string('phone', 20)->unique();
            $table->string('email')->nullable();
            $table->string('blood_group', 3);
            $table->string('area')->nullable();
            $table->string('district')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->enum('availability', ['available','maybe','unavailable'])->default('available');
            $table->date('last_donation_date')->nullable();
            $table->unsignedInteger('donation_count')->default(0);
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('consent_at')->nullable();
            $table->timestamps();

            $table->index(['blood_group','availability']);
            $table->index(['district','area']);
        });
    }
    public function down(): void { Schema::dropIfExists('donors'); }
};