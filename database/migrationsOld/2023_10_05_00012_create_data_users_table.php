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
		Schema::dropIfExists('data_users');
		Schema::create('data_users', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->unsignedBigInteger('user_id')->unique();
			$table->string('name');
			$table->string('mobile_phone')->nullable();
			$table->string('fix_phone')->nullable();
			$table->string('company_name')->nullable();
			$table->string('pic_name')->nullable();
			$table->string('jabatan')->nullable();
			$table->string('nip')->nullable();
			$table->string('ttd_img')->nullable();
			$table->string('digital_sign')->nullable();
			$table->string('npwp_company')->nullable();
			$table->string('nib_company')->nullable();
			$table->string('address_company');
			$table->string('provinsi')->nullable();
			$table->string('kabupaten')->nullable();
			$table->string('kecamatan')->nullable();
			$table->string('desa')->nullable();
			$table->string('kodepos')->nullable();
			$table->string('fax')->nullable();
			$table->string('ktp')->nullable();
			$table->string('ktp_image')->nullable();
			$table->string('assignment')->nullable();
			$table->string('avatar')->nullable();
			$table->string('logo')->nullable();
			$table->string('email_company')->nullable();
			$table->timestamps();
			$table->softDeletes();
			$table->foreign('user_id')
				->references('id')
				->on('users')
				->onUpdate('cascade')
				->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('data_users');
	}
};
