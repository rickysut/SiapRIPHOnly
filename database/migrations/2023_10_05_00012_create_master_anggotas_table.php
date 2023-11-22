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
		Schema::dropIfExists('master_anggotas');
		Schema::create('master_anggotas', function (Blueprint $table) {
			$table->bigIncrements('mst_id');
			$table->integer('id')->nullable();
			$table->string('npwp', 50);
			$table->string('anggota_id')->unique();
			$table->string('poktan_id');
			$table->string('nama_petani')->nullable();
			$table->string('ktp_petani')->nullable();
			$table->double('luas_lahan')->nullable();
			$table->string('periode_tanam')->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}
	public function down()
	{
		Schema::dropIfExists('master_anggotas');
	}
};
