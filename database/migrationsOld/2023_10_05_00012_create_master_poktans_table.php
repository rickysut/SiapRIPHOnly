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
        Schema::dropIfExists('master_poktans');
        Schema::create('master_poktans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('npwp', 50);
            $table->string('poktan_id');
            $table->string('id_provinsi')->nullable();
            $table->string('id_kabupaten')->nullable();
            $table->string('id_kecamatan')->nullable();
            $table->string('id_kelurahan')->nullable();
            $table->string('nama_kelompok')->nullable();
            $table->string('nama_pimpinan')->nullable();
            $table->string('hp_pimpinan')->nullable();
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
