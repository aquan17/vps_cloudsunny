<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProviderCostToVpsInstancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('vps_instances', function (Blueprint $table) {
            $table->unsignedBigInteger('provider_cost')->default(0)->after('paid_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('vps_instances', function (Blueprint $table) {
            $table->dropColumn('provider_cost');
        });
    }
}
