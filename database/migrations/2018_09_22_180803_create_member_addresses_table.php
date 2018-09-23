<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Migration auto-generated by Sequel Pro Laravel Export (1.4.1).
 * @see https://github.com/cviebrock/sequel-pro-laravel-export
 */
class CreateMemberAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->integer('member_id')->nullable();
            $table->string('realname', 191)->nullable();
            $table->string('phone', 191)->nullable();
            $table->string('province', 191)->nullable();
            $table->string('city', 191)->nullable();
            $table->string('area', 191)->nullable();
            $table->string('address', 191)->nullable();
            $table->string('zipcode', 191)->nullable();
            $table->tinyInteger('isdefault')->nullable()->default(0);
            $table->tinyInteger('type')->nullable()->default(1);
            $table->nullableTimestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('member_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_addresses');
    }
}
