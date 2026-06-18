<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeProxyToProxyInstancesTable extends Migration
{
    public function up(): void
    {
        Schema::table('proxy_instances', function (Blueprint $table) {
            $table->string('type_proxy', 16)->default('HTTP')->after('product_id');
        });
    }

    public function down(): void
    {
        Schema::table('proxy_instances', function (Blueprint $table) {
            $table->dropColumn('type_proxy');
        });
    }
}
