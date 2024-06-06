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
		Schema::dropIfExists('lokasi_checks');
		Schema::create('lokasi_checks', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->unsignedBigInteger('pengajuan_id');
			$table->unsignedBigInteger('commitcheck_id');
			$table->unsignedBigInteger('pkscheck_id');
			$table->unsignedBigInteger('poktan_id');
			$table->unsignedBigInteger('anggota_id');
			$table->string('npwp');
			$table->string('no_ijin');

			//pemeriksaan data online
			$table->string('onlinestatus')->nullable(); //
			$table->text('onlinenote')->nullable();
			$table->date('onlineverif_at')->nullable();
			$table->unsignedBigInteger('onlineverif_by')->nullable();

			//diisi oleh onfarm verifikator, data geolokasi
			$table->string('metode')->nullable();
			$table->text('latitude')->nullable();
			$table->text('longitude')->nullable();
			$table->text('altitude')->nullable();
			$table->text('polygon')->nullable();

			//data tanam
			$table->string('luas_verif')->nullable();
			$table->date('tgl_ukur')->nullable();

			//data produksi
			$table->string('volume_verif')->nullable();
			$table->date('tgl_timbang')->nullable();

			$table->string('onfarmstatus')->nullable();
			$table->text('onfarmnote')->nullable();
			$table->date('onfarmverif_at')->nullable();
			$table->date('onfarmverif_by')->nullable();
			$table->softDeletes();
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
		Schema::dropIfExists('lokasi_checks');
	}
};
