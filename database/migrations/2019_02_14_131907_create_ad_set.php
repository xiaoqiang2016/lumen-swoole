<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdSet extends Migration
{
    private $tableName = 't_ad_set';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create($this->tableName, function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
            $table->string('id',50);
            $table->string("account_id",255)->nullable(false)->default('')->comment("广告账户ID。");
            $table->string("campaign_id",255)->nullable(false)->default('')->comment("广告系列ID。");
            $table->string("name",255)->nullable(false)->default('')->comment("名称。");
            //PAUSED = 暂停
            $table->string("status",50)->nullable(false)->default('')->comment("当前状态。");
            $table->decimal("budget",20,4)->nullable(false)->default(0)->comment("预算");
            $table->dateTime("created_time")->nullable(true)->default(null)->comment("创建时间。");
            $table->integer("channel_id")->nullable(false)->default(0)->comment("渠道。");
            $table->timestamps();
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
        Schema::dropIfExists($this->tableName);
    }
}
