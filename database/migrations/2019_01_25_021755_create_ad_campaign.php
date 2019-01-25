<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdCampaign extends Migration
{
    private $tableName = 't_ad_campaign';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            //
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
            $table->string('id',50);
            $table->string("account_id",255)->nullable(false)->comment("广告账户ID");
            $table->string("name",255)->nullable(false)->comment("名称");
            $table->string("objective",50)->nullable(false)->comment("营销目标");
            //PAUSED = 暂停
            $table->string("status",50)->nullable(false)->comment("当前状态");

            $table->dateTime("start_time")->nullable(true)->comment("开始时间");
            $table->dateTime("created_time")->nullable(true)->comment("创建时间");
            $table->string("channel",30)->comment("渠道");
            $table->timestamps();
            $table->unique('id');
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop($this->tableName);
    }
}
