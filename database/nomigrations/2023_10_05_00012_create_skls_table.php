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
		Schema::create('skls', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('pengajuan_id');
			$table->string('no_pengajuan')->nullable();
			$table->string('no_skl')->nullable();
			$table->string('npwp')->nullable();
			$table->string('no_ijin')->nullable();
			$table->unsignedBigInteger('submit_by')->nullable();
			$table->date('published_date')->nullable();
			$table->string('qrcode')->nullable();
			$table->text('nota_attch')->nullable();
			$table->string('approved_by')->nullable();
			$table->string('approved_at')->nullable();
			$table->string('skl_upload')->nullable();
			$table->string('skl_auto')->nullable();
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
		Schema::dropIfExists('skls');
	}
};
