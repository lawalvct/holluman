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
            $table->string('n3tdata_status')->nullable()->after('data_activation_failed_at');
            $table->string('n3tdata_plan')->nullable()->after('n3tdata_status');
            $table->decimal('n3tdata_amount', 10, 2)->nullable()->after('n3tdata_plan');
            $table->string('n3tdata_phone_number')->nullable()->after('n3tdata_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'n3tdata_status',
                'n3tdata_plan',
                'n3tdata_amount',
                'n3tdata_phone_number'
            ]);
        });
    }
};
