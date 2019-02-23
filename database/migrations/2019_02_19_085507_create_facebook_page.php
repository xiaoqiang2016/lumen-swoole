<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacebookPage extends Migration
{
    private $table = 't_facebook_page';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->string("id",50)->nullable(false)->comment("ID。");
            $table->string("account_id",50)->nullable(false)->comment("广告账户ID");
            $table->string("page_id",50)->nullable(false)->comment("主页ID");
            $table->string('name',255)->nullable(false)->comment("名称。");
            $table->tinyInteger("status")->nullable(false)->comment("状态。0=未发布,1=已发布。");
            $table->dateTime('sync_time')->nullable(true)->comment("同步时间。");
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
        Schema::dropIfExists($this->table);
    }
}
