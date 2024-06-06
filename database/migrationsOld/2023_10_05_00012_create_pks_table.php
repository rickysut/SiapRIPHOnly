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
		Schema::dropIfExists('pks');
		Schema::create('pks', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('npwp')->nullable();
			$table->string('no_ijin')->nullable();
			$table->unsignedBigInteger('poktan_id')->nullable();
			$table->string('no_perjanjian')->nullable();
			$table->date('tgl_perjanjian_start')->nullable();
			$table->date('tgl_perjanjian_end')->nullable();
			$table->integer('jumlah_anggota')->nullable();
			$table->integer('luas_rencana')->nullable();
			$table->string('varietas_tanam')->nullable();
			$table->string('periode_tanam')->nullable();
			$table->string('provinsi_id')->nullable();
			$table->string('kabupaten_id')->nullable();
			$table->string('kecamatan_id')->nullable();
			$table->string('kelurahan_id')->nullable();
			$table->string('berkas_pks')->nullable();
			$table->string('status')->nullable();
			$table->text('note')->nullable();
			$table->bigInteger('verif_by')->nullable();
			$table->date('verif_at')->nullable();
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
		Schema::dropIfExists('pks');
	}
};
