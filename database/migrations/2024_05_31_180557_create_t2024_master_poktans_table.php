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
        Schema::create('t2024_master_poktans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kode_register', 50);
			$table->text('alamat')->nullable();
            $table->string('provinsi_id')->nullable();
            $table->string('kabupaten_id')->nullable();
            $table->string('kecamatan_id')->nullable();
            $table->string('kelurahan_id')->nullable();
            $table->string('nama_kelompok')->nullable();
            $table->string('nama_pimpinan')->nullable();
            $table->string('hp_pimpinan')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('t2024_master_poktans');
    }
};
