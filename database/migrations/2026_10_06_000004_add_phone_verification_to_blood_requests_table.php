<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('blood_requests', function (Blueprint $table) {
            $table->timestamp('requester_phone_verified_at')->nullable()->after('status');
        });
    }

    public function down(): void {
        Schema::table('blood_requests', function (Blueprint $table) {
            $table->dropColumn('requester_phone_verified_at');
        });
    }
};
