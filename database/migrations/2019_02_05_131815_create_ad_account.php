<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdAccount extends Migration
{
    private $tableName = 't_ad_account';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->string("id",50)->nullable(false)->comment("广告账户ID");
            $table->string("name",255)->nullable(false)->comment("广告账户名称。");
            $table->tinyInteger("local_status")->nullable(false)->default(1)->comment("本地状态。1=启用，0=停用");
            $table->integer('remote_status')->nullable(false)->default(1)->comment("远程状态。1=启用");
            $table->tinyInteger('channel_id')->nullable(false)->default(0)->comment("渠道ID。1=Facebook,3=Google");
            $table->dateTime('sync_at')->nullable(true)->comment("数据同步时间。");
            $table->dateTime('created_time')->nullable(true)->comment("账户创建时间。");
            $table->string('disable_reason',50)->nullable('')->comment("账户被禁用原因。");
            $table->decimal('balance',20,4)->nullable(true)->comment("余额。");
            $table->primary('id');
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
