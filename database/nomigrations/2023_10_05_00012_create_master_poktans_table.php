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
        Schema::create('master_poktans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('npwp', 50); //jangan digunakan
            $table->string('poktan_id'); //id dari riph
            $table->string('kode_register', 50);
			$table->text('alamat')->nullable();
            $table->string('id_provinsi')->nullable();
            $table->string('id_kabupaten')->nullable();
            $table->string('id_kecamatan')->nullable();
            $table->string('id_kelurahan')->nullable();
            $table->string('nama_kelompok')->nullable();
            $table->string('nama_pimpinan')->nullable();
            $table->string('hp_pimpinan')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_poktans');
    }
};
