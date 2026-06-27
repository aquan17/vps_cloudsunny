<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAffiliateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliate_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Người nhận hoa hồng
            $table->unsignedBigInteger('buyer_id'); // Người mua hàng (F1)
            $table->string('transaction_type', 50); // buy, renew
            $table->decimal('amount', 15, 2); // Tổng tiền đơn hàng
            $table->decimal('commission', 15, 2); // Hoa hồng 5%
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('buyer_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('affiliate_logs');
    }
}
