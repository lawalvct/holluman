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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreignId('network_id')->nullable()->after('subscription_plan_id')->constrained()->onDelete('set null');
            $table->string('subscriber_phone')->nullable()->after('network_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['network_id']);
            $table->dropColumn(['network_id', 'subscriber_phone']);
        });
    }
};
