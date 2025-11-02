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
            // Track total months for long-term subscriptions
            $table->integer('months_total')->default(1)->after('end_date')->comment('Total months for this subscription (1, 3, 6, 12)');

            // Track how many months have been activated on N3tdata
            $table->integer('months_activated')->default(0)->after('months_total')->comment('Number of months activated on N3tdata');

            // Track last N3tdata activation date
            $table->timestamp('last_n3tdata_activation_date')->nullable()->after('data_activated_at')->comment('Last time N3tdata was activated/renewed');

            // Track when next renewal is due
            $table->date('next_renewal_due_date')->nullable()->after('last_n3tdata_activation_date')->comment('Date when next N3tdata renewal is due');

            // Track if subscription needs renewal
            $table->boolean('needs_renewal')->default(false)->after('next_renewal_due_date')->comment('Flag indicating if subscription needs monthly renewal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'months_total',
                'months_activated',
                'last_n3tdata_activation_date',
                'next_renewal_due_date',
                'needs_renewal'
            ]);
        });
    }
};
