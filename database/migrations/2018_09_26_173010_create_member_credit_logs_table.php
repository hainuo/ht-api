<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Migration auto-generated by Sequel Pro Laravel Export (1.4.1).
 * @see https://github.com/cviebrock/sequel-pro-laravel-export
 */
class CreateMemberCreditLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_credit_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->integer('member_id')->nullable();
            $table->integer('operater_id')->nullable();
            $table->decimal('credit', 8, 2)->nullable()->default(0.00);
            $table->string('column')->nullable();
            $table->string('remark', 191)->nullable();
            $table->nullableTimestamps();

            $table->index('member_id', 'member_credit_logs_member_id_index');
            $table->index('type', 'member_credit_logs_type_index');
            $table->index('user_id', 'member_credit_logs_user_id_index');
            $table->index('operater_id', 'member_credit_logs_operater_id_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_credit_logs');
    }
}
