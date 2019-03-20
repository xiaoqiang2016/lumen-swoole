<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacebookOeTokenTable extends Migration
{
    private $tableName = 't_facebook_oe_token';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('token_id')->default(0)->nullable(false)->comment("token id");
            $table->integer('client_id')->default(0)->nullable(false)->comment('Client ID');
            $table->integer('user_id')->default(0)->nullable(false)->comment('用户ID');
            $table->string('link',2000)->default('')->nullable(false)->comment('地址。');
            $table->string('params',2000)->default('[]')->nullable(false)->comment('绑定参数');
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
