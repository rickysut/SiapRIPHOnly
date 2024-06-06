<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::dropIfExists('completeds');
		Schema::create('completeds', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('no_skl')->nullable();
			$table->string('periodetahun')->nullable();
			$table->string('no_ijin')->nullable();
			$table->string('npwp')->nullable();
			$table->date('published_date')->nullable();
			$table->decimal('luas_tanam')->nullable();
			$table->decimal('volume')->nullable();
			$table->string('status')->nullable();
			$table->string('skl_upload')->nullable();
			$table->text('url')->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('completeds');
	}
};
