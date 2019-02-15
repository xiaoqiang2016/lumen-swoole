<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdAccountBelong extends Migration
{
    private $tableName = 't_ad_account_belong';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id')->nullable(false)->default(0)->comment("客户ID。");
            $table->integer('user_id')->nullable(false)->default(0)->comment("用户ID。");
            $table->integer('channel_id')->nullable(false)->default(0)->comment("渠道ID。");
            $table->string('account_id',50)->nullable(false)->default('')->comment("广告账户ID。");
            $table->integer('token_id')->nullable(false)->default(0)->comment("授权ID。");
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
        Schema::dropIfExists($this->tableName);
    }
}
