<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTAdDiagnoseLogTable extends Migration
{
    private $tableName = 't_ad_diagnose_log';
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->string('account_id',50)->nullable(false)->comment("广告账号。");
            $table->string('group',50)->nullable(false)->comment("组名。");
            $table->string('handle',50)->nullable(false)->comment("处理器。");
            $table->string('name',50)->nullable(false)->comment("处理名称。");
            $table->string('count',50)->nullable(false)->comment("总数。");
            $table->string('point',50)->nullable(false)->comment("得分。");
            $table->integer('fail')->nullable(false)->comment("失败数量。");
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
