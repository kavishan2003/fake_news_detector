<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fakeness_checks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('url');
            $table->integer('score')->nullable();
            $table->string('title')->nullable();
            $table->text('image')->nullable();
            $table->longText('explanation')->nullable();
            $table->string('slug')->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fakeness_checks');
    }
};
