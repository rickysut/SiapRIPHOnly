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
		Schema::create('saprodis', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('kode_spatial')->nullable(); // untuk kebutuhan mendatang, by lokasi
			$table->string('ktp_petani')->nullable(); // untuk kebutuhan mendatang, by petani
			$table->string('poktan_id')->nullable(); // untuk kebutuhan mendatang, by poktan
			$table->unsignedBigInteger('pks_id')->nullable();
			$table->string('npwp')->nullable();
			$table->string('no_ijin')->nullable();
			$table->date('tanggal_saprodi')->nullable();
			$table->string('kategori')->nullable();
			$table->string('jenis')->nullable();
			$table->decimal('volume')->nullable();
			$table->string('satuan')->nullable();
			$table->integer('harga')->nullable();
			$table->string('file')->nullable();
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
		Schema::dropIfExists('saprodis');
	}
};
