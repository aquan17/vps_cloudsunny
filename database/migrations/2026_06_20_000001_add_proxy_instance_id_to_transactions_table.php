<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProxyInstanceIdToTransactionsTable extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('proxy_instance_id')
                ->nullable()
                ->after('vps_instance_id')
                ->constrained()
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['proxy_instance_id']);
            $table->dropColumn('proxy_instance_id');
        });
    }
}
