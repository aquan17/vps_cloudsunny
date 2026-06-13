<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProxyInstancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('proxy_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cloudsunny_account_id')->nullable()->constrained('cloud_sunny_accounts')->nullOnDelete();
            $table->unsignedBigInteger('provider_proxy_id')->nullable()->index();
            $table->unsignedBigInteger('provider_order_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            
            $table->string('ip')->nullable();
            $table->string('port')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            
            $table->string('sock5_port')->nullable();
            $table->string('sock5_username')->nullable();
            $table->string('sock5_password')->nullable();
            
            $table->string('status')->default('progressing');
            $table->string('billing_cycle')->default('monthly');
            
            $table->decimal('cost_monthly_usd', 8, 4)->nullable();
            $table->integer('paid_amount')->default(0);
            
            $table->timestamp('expires_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proxy_instances');
    }
}
