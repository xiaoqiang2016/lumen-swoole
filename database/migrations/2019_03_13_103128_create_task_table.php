<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskTable extends Migration
{
    private $tableName = 't_task';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->string('action',50)->nullable(false)->default('')->comment("动作名。");
            $table->string('params',5000)->nullable(false)->default('[]')->comment("执行参数。");
            $table->string('status',50)->nullable(false)->default(0)->comment("执行状态。");
            $table->integer('retry')->nullable(false)->default(10)->comment("重试次数。");
            $table->integer('max_retry')->nullable(false)->default(10)->comment("重试次数上限。");
            $table->string('log',5000)->nullable(false)->default(10)->comment("执行日志。");
            $table->dateTime('exec_at')->nullable(true)->comment("执行时间。");
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
