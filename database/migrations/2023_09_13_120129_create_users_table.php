<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->longText('username');
            $table->longText('email');
            $table->text('password');
            $table->string('profile_photo')->nullable();
            $table->string('role')->default('user');
            $table->boolean('is_email_verified')->default(0);
            $table->timestamps();
        });
     }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
