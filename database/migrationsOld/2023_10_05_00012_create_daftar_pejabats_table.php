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
		Schema::dropIfExists('daftar_pejabats');
		Schema::create('daftar_pejabats', function (Blueprint $table) {
			$table->id();
			$table->string('nama')->nullable();
			$table->string('nip')->nullable();
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
		Schema::dropIfExists('daftar_pejabats');
	}
};
