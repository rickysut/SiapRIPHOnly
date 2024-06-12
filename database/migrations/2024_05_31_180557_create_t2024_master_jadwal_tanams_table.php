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
        Schema::create('t2024_master_jadwal_tanams', function (Blueprint $table) {
            $table->id();
			$table->string('kode_spatial', 16);
			$table->date('awal_masa')->nullable();
			$table->date('akhir_masa')->nullable();
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
        Schema::dropIfExists('t2024_master_jadwal_tanams');
    }
};
