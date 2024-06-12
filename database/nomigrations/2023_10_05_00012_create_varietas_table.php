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
		Schema::create('varietas', function (Blueprint $table) {
			$table->id();
			$table->string('kode_komoditas')->nullable();
			$table->string('nama_komoditas')->nullable();
			$table->string('kode_varietas')->nullable();
			$table->string('nama_varietas')->nullable();
			$table->text('keterangan')->nullable();
			$table->string('datalain')->nullable();
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
		Schema::dropIfExists('varietas');
	}
};
