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
        Schema::create('n3tdata_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('network_id')->constrained('networks')->onDelete('cascade');
            $table->string('plan_type');
            $table->string('plan_name');
            $table->decimal('amount', 10, 2);
            $table->string('duration');
            $table->timestamps();

            // Add indexes for better query performance
            $table->index('network_id');
            $table->index('plan_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('n3tdata_plans');
    }
};
