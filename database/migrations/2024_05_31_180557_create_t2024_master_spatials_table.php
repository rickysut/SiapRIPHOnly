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
        Schema::create('t2024_master_spatials', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('komoditas')->nullable(); //saat ini default Bawang Putih
			$table->string('kode_spatial', 16)->unique()->nullable(); //contoh: KAB-XX-YY-1234
			// $table->foreign('ktp_petani')->references('ktp_petani')->on('master_anggotas')->onDelete('cascade'); // Menambahkan foreign key
			$table->integer('poktan_id')->nullable();
			$table->string('ktp_petani', 16); //contoh: 3313022204510001
			$table->text('latitude');
			$table->text('longitude');
			$table->text('polygon');
			$table->double('altitude')->nullable();
			$table->text('imagery')->nullable();
			$table->double('luas_lahan');
			$table->string('nama_lahan', 55)->nullable();
			$table->text('catatan')->nullable();
			$table->string('provinsi_id', 2); //contoh: 11
			$table->string('kabupaten_id', 4); //contoh: 1101
			$table->string('kecamatan_id', 7); //contoh: 1101010
			$table->string('kelurahan_id', 10); //contoh: 1101010003
			$table->string('nama_petugas')->nullable();
			$table->string('file_url');
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
        Schema::dropIfExists('t2024_master_spatials');
    }
};
