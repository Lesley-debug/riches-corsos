<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('puppies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('breed')->default('Cane Corso');
            $table->date('date_of_birth');
            $table->enum('sex', ['male', 'female']);
            $table->decimal('price', 10, 2);
            $table->enum('status', ['available', 'pending', 'reserved', 'sold'])->default('available');
            $table->text('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('puppies');
    }
};
