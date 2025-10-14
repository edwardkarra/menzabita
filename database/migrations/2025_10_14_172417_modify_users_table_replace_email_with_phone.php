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
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_email_unique');
            $table->dropColumn(['email', 'email_verified_at']);
            $table->string('phone')->nullable()->after('name');
            $table->timestamp('phone_verified_at')->nullable()->after('phone');
        });
        
        // Make phone unique after adding it
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->unique()->change();
        });
        
        // Update password_reset_tokens table to use phone instead of email
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->dropPrimary();
            $table->dropColumn('email');
            $table->string('phone')->primary()->first();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'phone_verified_at']);
            $table->string('email')->unique()->after('name');
            $table->timestamp('email_verified_at')->nullable()->after('email');
        });
        
        // Revert password_reset_tokens table to use email
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->dropPrimary();
            $table->dropColumn('phone');
            $table->string('email')->primary()->first();
        });
    }
};
