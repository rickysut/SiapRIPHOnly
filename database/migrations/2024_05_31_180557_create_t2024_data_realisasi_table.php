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
		Schema::create('t2024_data_realisasi', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('npwp_company');
			$table->string('no_ijin');
			$table->bigInteger('poktan_id');
			$table->bigInteger('pks_id');
			$table->string('ktp_petani', 16);
			$table->bigInteger('anggota_id');
			$table->bigInteger('lokasi_id');
			$table->string('kode_spatial')->nullable();

			//data spasial
			$table->string('nama_lokasi')->nullable();
			$table->text('latitude')->nullable();
			$table->text('longitude')->nullable();
			$table->text('polygon')->nullable();
			$table->double('altitude')->nullable();
			$table->decimal('luas_kira', 8, 2)->nullable();

			//data tanam
			$table->date('mulai_tanam')->nullable();
			$table->date('akhir_tanam')->nullable();
			$table->double('luas_lahan')->nullable();
			$table->json('foto_tanam')->nullable();

			//data produksi
			$table->date('mulai_panen')->nullable();
			$table->date('akhir_panen')->nullable();
			$table->decimal('volume')->nullable();
			$table->json('foto_panen')->nullable();

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
		Schema::dropIfExists('data_realisasi');
	}
};
