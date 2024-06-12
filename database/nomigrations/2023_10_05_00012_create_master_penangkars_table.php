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
		Schema::create('master_penangkars', function (Blueprint $table) {
			$table->id();
			$table->string('npwp')->nullable();
			$table->string('nama_lembaga');
			$table->string('nama_pimpinan')->nullable();
			$table->string('hp_pimpinan')->nullable();
			$table->string('alamat')->nullable();
			$table->string('provinsi_id')->nullable();
			$table->string('kabupaten_id')->nullable();
			$table->string('kecamatan_id')->nullable();
			$table->string('kelurahan_id')->nullable();
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
		Schema::dropIfExists('master_penangkars');
	}
};
