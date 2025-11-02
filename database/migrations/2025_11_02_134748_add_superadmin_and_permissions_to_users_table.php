<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add superadmin flag (only user ID 1 will have this)
            $table->boolean('is_superadmin')->default(false)->after('role');

            // Add JSON column for granular permissions
            $table->json('permissions')->nullable()->after('is_superadmin');
        });

        // Set user ID 1 as superadmin with all permissions
        DB::table('users')->where('id', 1)->update([
            'is_superadmin' => true,
            'role' => 'admin',
            'permissions' => json_encode([
                'dashboard' => true,
                'users' => true,
                'sims' => true,
                'plans' => true,
                'subscriptions' => true,
                'payments' => true,
                'networks' => true,
                'reports' => true,
                'settings' => true,
                'admin_management' => true, // Special permission to manage other admins
            ])
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_superadmin', 'permissions']);
        });
    }
};
