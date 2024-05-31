<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('users_verify', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->string('token');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down() : void
    {
        Schema::dropIfExists('users_verify');
    }
};
