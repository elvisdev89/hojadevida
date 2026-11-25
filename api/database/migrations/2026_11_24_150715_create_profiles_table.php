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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('identification_type', 10);
            $table->string('identification_number', 30);
            $table->date('birth_date');
            $table->string('phone', 30);
            $table->string('address')->nullable();
            $table->string('city', 100);
            $table->string('photo_path')->nullable();
            $table->text('summary')->nullable();
            $table->timestamps();

            $table->unique(['identification_type', 'identification_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
