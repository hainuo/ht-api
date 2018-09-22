<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration auto-generated by Sequel Pro Laravel Export (1.4.1)
 * @see https://github.com/cviebrock/sequel-pro-laravel-export
 */
class CreateShopCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create ('shop_coupons', function (Blueprint $table) {
            $table->increments ('id');
            $table->integer ('user_id')->nullable ();
            $table->integer ('merch_id')->nullable ();
            $table->integer ('category_id')->nullable ();
            $table->string ('name', 191)->nullable ();
            $table->integer ('total')->nullable ();
            $table->integer ('max_receive')->nullable ();
            $table->decimal ('enough', 8, 2)->nullable ();
            $table->tinyInteger ('coupon_type')->nullable ();
            $table->tinyInteger ('is_show')->nullable ();
            $table->tinyInteger ('discount_type')->nullable ();
            $table->decimal ('discount', 8, 2)->nullable ();
            $table->decimal ('deduct', 8, 2)->nullable ();
            $table->integer ('limit_type')->nullable ();
            $table->integer ('limit_days')->nullable ();
            $table->timestamp ('time_start')->nullable ();
            $table->timestamp ('time_end')->nullable ();
            $table->tinyInteger ('is_limit_goods')->nullable ();
            $table->string ('limit_goods_ids', 11)->nullable ();
            $table->tinyInteger ('is_limit_category')->nullable ();
            $table->string ('limit_category_ids', 11)->nullable ();
            $table->tinyInteger ('is_limit_level')->nullable ();
            $table->string ('limit_level_ids', 11)->nullable ();
            $table->tinyInteger ('is_limit_agent')->nullable ();
            $table->string ('limit_agent_ids', 11)->nullable ();
            $table->string ('description', 191)->nullable ();
            $table->integer ('sort')->nullable ()->default (0);
            $table->tinyInteger ('status')->nullable ();
            $table->softDeletes ();
            $table->nullableTimestamps ();

            $table->index ('user_id');
            $table->index ('merch_id');
            $table->index ('category_id');
            $table->index ('name');


        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists ('shop_coupons');
    }
}
