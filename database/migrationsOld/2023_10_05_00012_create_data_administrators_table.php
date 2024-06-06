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
		Schema::dropIfExists('data_administrators');
		Schema::create('data_administrators', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('user_id');
			$table->string('nama')->nullable();
			$table->string('jabatan')->nullable();
			$table->string('nip')->nullable();
			$table->string('sign_img')->nullable();
			$table->string('digital_sign')->nullable();
			$table->unsignedTinyInteger('status')->nullable();
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
		Schema::dropIfExists('data_administrators');
	}
};
