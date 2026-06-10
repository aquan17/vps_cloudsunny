<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAutomatedPaymentFieldsToTopupRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('topup_requests', function (Blueprint $table) {
            $table->string('code')->nullable()->after('id')->unique();
            $table->string('provider')->default('payos')->after('status');
            $table->bigInteger('provider_order_code')->nullable()->after('provider');
            $table->string('transaction_ref')->nullable()->after('provider_order_code');
            $table->json('raw_payload')->nullable()->after('transaction_ref');
            $table->timestamp('paid_at')->nullable()->after('raw_payload');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('topup_requests', function (Blueprint $table) {
            $table->dropColumn([
                'code',
                'provider',
                'provider_order_code',
                'transaction_ref',
                'raw_payload',
                'paid_at'
            ]);
        });
    }
}
