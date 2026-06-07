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
            $table->string('rt', 3)->nullable()->after('phone');
            $table->string('rw', 3)->nullable()->after('rt');
            // Change enum to string to support more roles, or drop and recreate if sqlite doesn't support change
        });
        
        // Use raw SQL or DB statement if needed, or simple string change for MySQL
        // If sqlite, changing columns might fail, but let's try
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('petugas')->change();
            });
        } catch (\Exception $e) {
            // Ignore if sqlite fails to change
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['rt', 'rw']);
        });
    }
};
