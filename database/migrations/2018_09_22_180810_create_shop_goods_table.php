<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Migration auto-generated by Sequel Pro Laravel Export (1.4.1).
 * @see https://github.com/cviebrock/sequel-pro-laravel-export
 */
class CreateShopGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_goods', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->integer('member_id')->nullable();
            $table->integer('merch_id')->nullable();
            $table->string('title', 191)->nullable();
            $table->string('keywords', 191)->nullable();
            $table->string('short_title', 191)->nullable();
            $table->string('thumb', 191)->nullable();
            $table->string('description', 191)->nullable();
            $table->text('content')->nullable();
            $table->string('goodssn', 191)->nullable();
            $table->string('productsn', 191)->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->decimal('old_price', 8, 2)->nullable();
            $table->decimal('cost_price', 8, 2)->nullable();
            $table->decimal('min_price', 8, 2)->nullable();
            $table->decimal('max_price', 8, 2)->nullable();
            $table->integer('total')->nullable();
            $table->tinyInteger('totalcnf')->nullable();
            $table->integer('sales')->nullable();
            $table->integer('real_sales')->nullable();
            $table->tinyInteger('show_sales')->nullable()->default(1);
            $table->tinyInteger('show_spec')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->string('credit', 191)->nullable();
            $table->integer('minbuy')->nullable();
            $table->integer('maxbuy')->nullable();
            $table->integer('total_maxbuy')->nullable();
            $table->tinyInteger('hasoption')->nullable();
            $table->tinyInteger('isnew')->nullable();
            $table->tinyInteger('ishot')->nullable();
            $table->tinyInteger('isrecommand')->nullable();
            $table->tinyInteger('isdiscount')->nullable();
            $table->string('discount_title', 191)->nullable();
            $table->timestamp('discount_end')->nullable();
            $table->timestamp('discount_price')->nullable();
            $table->tinyInteger('issendfree')->nullable();
            $table->tinyInteger('iscomment')->nullable();
            $table->integer('views')->nullable()->default(0);
            $table->tinyInteger('hascommission')->nullable()->default(0);
            $table->decimal('commission0_rate', 8, 2)->nullable()->default(0.00);
            $table->decimal('commission0_pay', 8, 2)->nullable()->default(0.00);
            $table->decimal('commission1_rate', 8, 2)->nullable()->default(0.00);
            $table->decimal('commission1_pay', 8, 2)->nullable()->default(0.00);
            $table->decimal('commission2_rate', 8, 2)->nullable()->default(0.00);
            $table->decimal('commission2_pay', 8, 2)->nullable()->default(0.00);
            $table->decimal('commission3_rate', 8, 2)->nullable()->default(0.00);
            $table->decimal('commission3_pay', 8, 2)->nullable()->default(0.00);
            $table->tinyInteger('is_not_discount')->nullable();
            $table->tinyInteger('deduct_credit1')->nullable();
            $table->tinyInteger('deduct_credit2')->nullable();
            $table->tinyInteger('dispatch_type')->nullable();
            $table->integer('dispatch_id')->nullable();
            $table->decimal('dispatch_price', 8, 2)->nullable();
            $table->string('province', 191)->nullable();
            $table->string('city', 191)->nullable();
            $table->string('tags', 191)->nullable();
            $table->tinyInteger('show_total')->nullable();
            $table->tinyInteger('auto_receive')->nullable()->default(0);
            $table->tinyInteger('can_not_refund')->nullable();
            $table->tinyInteger('type')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->integer('sort')->nullable()->default(0);
            $table->nullableTimestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('merch_id');
            $table->index('type');
            $table->index('status');
            $table->index('created_at');
            $table->index('title');
            $table->index('keywords');
            $table->index('description');
            $table->index('short_title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_goods');
    }
}
