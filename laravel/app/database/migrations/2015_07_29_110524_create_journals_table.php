<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJournalsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('journals', function(Blueprint $table)
		{
            $table->engine = 'MYISAM';
			$table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->date('publish_date');
            $table->integer('volume');
            $table->integer('day');
            $table->text('contents');
            $table->text('special_events');
			$table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('journals');
	}

}
