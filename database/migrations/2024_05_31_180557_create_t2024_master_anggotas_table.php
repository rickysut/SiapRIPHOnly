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
		Schema::create('t2024_master_anggotas', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('npwp', 50); //jangan digunakan dimanapun
			$table->string('anggota_id')->unique(); //id dari riph jangan digunakan dimanapun
			$table->string('poktan_id'); //jangan digunakan dimanapun
			$table->string('nama_petani')->nullable();
			$table->string('ktp_petani', 18)->nullable()->unique();
			$table->string('hp_petani', 20)->nullable();
			$table->text('alamat_petani')->nullable();
			$table->bigInteger('kelurahan_id')->nullable();
			$table->integer('kecamatan_id')->nullable();
			$table->integer('kabupaten_id')->nullable();
			$table->integer('provinsi_id')->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}
	public function down()
	{
		Schema::dropIfExists('t2024_master_anggotas');
	}
};
