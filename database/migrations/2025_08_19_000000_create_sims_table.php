<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('sim_number')->unique();
            $table->string('camera_name');
            $table->string('camera_location');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sims');
    }
};
