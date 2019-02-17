<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdDiagnosis extends Migration
{
    private $table = 't_ad_diagnosis';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string("account_id",50)->nullable(false)->default('')->comment("广告账户ID。");
            $table->string("campaign_id",50)->nullable(true)->default('')->comment("广告系列ID。");
            $table->string("set_id",50)->nullable(true)->default('')->comment("广告组ID。");
            $table->string("ad_id",50)->nullable(true)->default('')->comment("广告ID。");
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
