<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('vps_instances', function (Blueprint $table) {
            $table->foreignId('cloudsunny_account_id')->nullable()->after('user_id')->constrained('cloud_sunny_accounts')->nullOnDelete();
            $table->unsignedBigInteger('provider_vps_id')->nullable()->after('cloudsunny_account_id');
            $table->unsignedBigInteger('provider_order_id')->nullable()->after('provider_vps_id');
            $table->unsignedInteger('provider_product_id')->nullable()->after('provider_order_id');
            $table->unsignedInteger('provider_os_id')->nullable()->after('provider_product_id');
            $table->string('billing_cycle', 32)->nullable()->after('provider_os_id');
            $table->string('login_username', 120)->nullable()->after('public_ip');
            $table->json('provider_payload')->nullable()->after('login_username');

            $table->index(['cloudsunny_account_id', 'status']);
            $table->index('provider_vps_id');
        });
    }

    public function down()
    {
        Schema::table('vps_instances', function (Blueprint $table) {
            $table->dropIndex(['cloudsunny_account_id', 'status']);
            $table->dropIndex(['provider_vps_id']);
            $table->dropConstrainedForeignId('cloudsunny_account_id');
            $table->dropColumn([
                'provider_vps_id',
                'provider_order_id',
                'provider_product_id',
                'provider_os_id',
                'billing_cycle',
                'login_username',
                'provider_payload',
            ]);
        });
    }
};
