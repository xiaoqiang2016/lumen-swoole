<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdCampaignInsight extends Migration
{
    private $tableName = 't_ad_campaign_insight';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("account_id",50)->nullable(false)->comment("广告账户ID。");
            $table->tinyInteger('channel_id')->nullable(false)->default(0)->comment("渠道ID。1=Facebook,3=Google");
            $table->string('campaign_id',50)->nullable(false)->default(0)->comment("广告系列ID。");
            $table->integer('reach')->nullable(false)->default(0)->comment("覆盖人数。");
            $table->unsignedDecimal('frequency',5,2)->nullable(false)->default(0)->comment("频次。");
            $table->unsignedDecimal('budget',16,4)->nullable(false)->default(0)->comment("预算。");
            $table->unsignedDecimal('spend',16,4)->nullable(false)->default(0)->comment("消耗。");
            $table->unsignedInteger('impressions')->nullable(false)->default(0)->comment("展示次数。");
            $table->unsignedDecimal('cpm',11,2)->nullable(false)->default(0)->comment("千次展示费用。");
            $table->unsignedDecimal('clicks',11,2)->nullable(false)->default(0)->comment("链接点击量。");
            $table->unsignedDecimal('cpc',11,2)->nullable(false)->default(0)->comment("单次链接点击费用。");
            $table->unsignedDecimal('ctr',11,2)->nullable(false)->default(0)->comment("链接点击率。");
            $table->unsignedDecimal('cpi',11,2)->nullable(false)->default(0)->comment("单次成效费用。");

            $table->integer('purchase')->nullable(false)->default(0)->comment("网站购物。");
            $table->string('effect',1000)->nullable(false)->default(0)->comment("成效。");
            $table->dateTime('start_time')->nullable(false)->default(NULL)->comment("数据日期(开始)。");
            $table->dateTime('end_time')->nullable(false)->default(NULL)->comment("数据日期(结束)。");
            $table->integer('conversions')->nullable(false)->default(0)->comment("转化次数。");

            $table->unsignedDecimal('costconv',11,2)->nullable(false)->default(0)->comment("单次转化费用。");

            $table->integer('interactions')->nullable(false)->default(0)->comment("互动数。");
            $table->unsignedDecimal('cir',11,2)->nullable(false)->default(0)->comment("互动。");
            $table->unsignedDecimal('avgcost',11,2)->nullable(false)->default(0)->comment("平均费用。");
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
