<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vps_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->text('root_password');
            $table->string('region', 32);
            $table->string('plan_id', 32);
            $table->string('status')->default('Đang khởi tạo...');
            $table->string('public_ip', 45)->nullable();
            $table->unsignedTinyInteger('cpu')->default(1);
            $table->unsignedSmallInteger('ram')->default(1);
            $table->unsignedSmallInteger('disk')->default(25);
            $table->decimal('cost_monthly_usd', 10, 4);
            $table->decimal('hourly_price_usd', 10, 6)->nullable();
            $table->unsignedBigInteger('paid_amount')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vps_instances');
    }
};
