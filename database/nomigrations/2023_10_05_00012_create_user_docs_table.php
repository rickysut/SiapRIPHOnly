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
		Schema::create('user_docs', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('npwp');
			$table->unsignedBigInteger('commitment_id');
			$table->string('no_ijin');

			//dokumen tanam
			$table->string('spvt')->nullable(); //surat pengajuan verifikasi tanam
			$table->string('sptjm')->nullable();
			$table->string('sptjmtanam')->nullable();
			$table->string('rta')->nullable();
			$table->string('sphtanam')->nullable();
			$table->string('spdst')->nullable(); //surat pengantar dinas telah selesai tanam
			$table->string('logbooktanam')->nullable();

			//hasil periksa dok tanam
			$table->string('spvtcheck')->nullable(); //surat pengajuan verifikasi tanam
			$table->string('sptjmcheck')->nullable();
			$table->string('sptjmtanamcheck')->nullable();
			$table->string('rtacheck')->nullable();
			$table->string('sphtanamcheck')->nullable();
			$table->string('spdstcheck')->nullable(); //surat pengantar dinas telah selesai tanam
			$table->string('logbooktanamcheck')->nullable();
			$table->bigInteger('tanamcheck_by')->nullable();
			$table->date('tanamverif_at')->nullable();

			//dokumen produksi
			$table->string('spvp')->nullable(); //surat pengajuan verifikasi produksi
			$table->string('sptjmproduksi')->nullable();
			$table->string('rpo')->nullable();
			$table->string('formLa')->nullable();
			$table->string('sphproduksi')->nullable();
			$table->string('spdsp')->nullable(); //surat pengantar dinas telah selesai produksi
			$table->string('logbookproduksi')->nullable();

			// hasil periksa dok produksi
			$table->string('spvpcheck')->nullable(); //surat pengajuan verifikasi produksi
			$table->string('sptjmproduksicheck')->nullable();
			$table->string('rpocheck')->nullable();
			$table->string('formLacheck')->nullable();
			$table->string('sphproduksicheck')->nullable();
			$table->string('spdspcheck')->nullable(); //surat pengantar dinas telah selesai produksi
			$table->string('logbookproduksicheck')->nullable();
			$table->bigInteger('prodcheck_by')->nullable();
			$table->date('prodverif_at')->nullable();

			// dokumen ajuan skl
			$table->string('spskl')->nullable(); //surat pengajuan verifikasi produksi
			$table->string('spsklcheck')->nullable(); //hasil periksa
			$table->bigInteger('spsklcheck_by')->nullable();
			$table->date('spsklverif_at')->nullable();

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
		Schema::dropIfExists('user_docs');
	}
};
