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
            $table->string('n3tdata_request_id')->nullable()->after('payment_reference');
            $table->json('n3tdata_response')->nullable()->after('n3tdata_request_id');
            $table->timestamp('data_activated_at')->nullable()->after('n3tdata_response');
            $table->timestamp('data_activation_failed_at')->nullable()->after('data_activated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'n3tdata_request_id',
                'n3tdata_response',
                'data_activated_at',
                'data_activation_failed_at'
            ]);
        });
    }
};
