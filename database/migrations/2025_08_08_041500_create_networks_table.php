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
        Schema::create('networks', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Network name e.g., MTN, Airtel, Glo
            $table->string('code', 10)->unique(); // Short code e.g., MTN, ART, GLO
            $table->string('full_name')->nullable(); // Full name e.g., Mobile Telephone Network
            $table->text('description')->nullable(); // Description of the network
            $table->string('image')->nullable(); // Logo/image path
            $table->string('color', 7)->nullable(); // Brand color in hex format
            $table->enum('type', ['mobile', 'broadband', 'fiber', 'satellite'])->default('mobile');
            $table->boolean('is_active')->default(true);
            $table->decimal('coverage_percentage', 5, 2)->nullable(); // Coverage percentage
            $table->json('service_areas')->nullable(); // Array of states/regions covered
            $table->json('contact_info')->nullable(); // Phone, email, website
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('networks');
    }
};
