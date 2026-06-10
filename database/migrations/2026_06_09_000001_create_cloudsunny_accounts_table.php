<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cloud_sunny_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('api_username');
            $table->string('api_app');
            $table->text('api_secret');
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->unsignedBigInteger('credit_vnd')->default(0);
            $table->unsignedBigInteger('total_vnd')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_full')->default(false);
            $table->unsignedInteger('priority')->default(0);
            $table->timestamp('last_synced_at')->nullable();
            $table->text('sync_error')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cloud_sunny_accounts');
    }
};
