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
		Schema::dropIfExists('penangkar_riph');
		Schema::create('penangkar_riph', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->BigInteger('penangkar_id')->nullable();
			$table->unsignedBigInteger('commitment_id')->nullable();
			$table->string('npwp', 50)->nullable();
			$table->string('no_ijin')->nullable();
			$table->string('varietas')->nullable();
			$table->string('ketersediaan')->nullable();
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
		Schema::dropIfExists('penangkar_riph');
	}
};
