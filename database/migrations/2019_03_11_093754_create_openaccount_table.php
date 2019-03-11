<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOpenaccountTable extends Migration
{
    private $tableName = 't_openaccount_facebook';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->string("status",50)->nullable(false)->default("")->comment("状态。");
            $table->integer('apply_number')->nullable(false)->default(0)->comment("申请数量。");
            $table->integer('bind_bm_id')->nullable(false)->default(0)->comment("绑定的bm id。");
            $table->string('business_license',500)->nullable(false)->default("")->comment("营业执照图片。");
            $table->string('business_code',500)->nullable(false)->default("")->comment("营业执照编号。");
            $table->string('address_cn',500)->nullable(false)->default("")->comment("公司中文地址。");
            $table->string('address_en',500)->nullable(false)->default("")->comment("公司英文地址。");
            $table->string('business_name_cn',500)->nullable(false)->default("")->comment("公司中文名称。");
            $table->string('business_name_en',500)->nullable(false)->default("")->comment("公司英文名称。");
            $table->string('city',50)->nullable(false)->comment("所在城市。");
            $table->string('email',255)->nullable(false)->comment("联系邮件。");
            $table->string('website',255)->nullable(false)->comment("网站地址。");
            $table->string("mobile",255)->nullable(false)->comment("手机号码。");
            $table->string("mobile_id",50)->nullable(false)->comment("手机号码id。");
            $table->string("promotable_urls",1000)->nullable(false)->default("")->comment("推广url列表。");
            $table->string("promotable_page_ids",1000)->nullable(false)->default("")->comment("主页id列表。");
            $table->string("promotable_app_ids",1000)->nullable(false)->default("")->comment("APP id列表。");
            $table->integer('timezone_id')->nullable(false)->default(0)->comment("时区ID。");
            $table->integer('facebook_user_id')->nullable(false)->default(0)->comment("Facebook用户ID。");
            $table->string("zip_code",50)->nullable(false)->default("")->comment("邮政编码。");
            $table->dateTime("oe_remote_created")->nullable(true)->comment("OE创建时间。");
            $table->dateTime("oe_remote_updated")->nullable(true)->comment("OE更新时间。");
            $table->dateTime("fb_remote_created")->nullable(true)->comment("Facebook审核创建时间。");
            $table->dateTime("fb_remote_updated")->nullable(true)->comment("Facebook审核更新时间。");
            $table->string("oe_id",50)->nullable(false)->default("")->comment("oe id。");
            $table->string("oe_token",500)->nullable(false)->default("")->comment("token值。");
            $table->string("oe_token_id",50)->nullable(true)->comment("token值ID。");
            $table->string("vertical",100)->nullable(false)->default("")->comment("一级行业分类。");
            $table->string("subvertical",100)->nullable(false)->default("")->comment("二级行业分类。");
            $table->string("oe_change_reasons",2000)->nullable(false)->default("[]")->comment("OE审核错误信息。");
            $table->string("request_change_reasons",2000)->nullable(false)->default("[]")->comment("FB审核错误信息。");
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
