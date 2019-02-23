<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdDiagnose extends Migration
{
    private $table = 't_ad_dianose';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string("account_id",50)->nullable(false)->comment("广告账户ID。");
            $table->string("campaign_id",50)->nullable(false)->default('')->comment("广告组ID。");
            $table->string("set_id",50)->nullable(false)->default('')->comment("广告系列ID。");
            $table->string("ad_id",50)->nullable(false)->default('')->comment("广告ID。");
            $table->string("group",50)->nullable(false)->default('')->comment("所属组。");
            $table->string("handle",50)->nullable(false)->comment("处理模块。");
            $table->string("name",50)->nullable(false)->default('')->comment("诊断名称。");
            $table->string("desc",255)->nullable(false)->default('')->comment("诊断结果说明。");
            $table->string("status",20)->nullable(false)->default('')->comment("诊断结果。success:成功。fail:失败。pass:略过。ignore:屏蔽");
            $table->string("addno",500)->nullable(false)->default('')->comment("定位参数。");
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
        Schema::dropIfExists($this->table);
    }
}
