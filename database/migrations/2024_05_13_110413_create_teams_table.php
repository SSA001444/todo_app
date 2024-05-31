<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up() : void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Add team_id to users table
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('team_id')->nullable()->after('id');

            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
        });
    }

    public function down() : void
    {
        Schema::whenTableHasColumn('users', 'team_id', function (Blueprint $table){
            $table->dropForeign(['team_id']);
        });

        Schema::dropIfExists('teams');
    }
};
