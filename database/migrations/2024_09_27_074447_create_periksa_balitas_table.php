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
        Schema::create('periksa_balitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bayis_id')->constrained('bayis')->onDelete('cascade');
            $table->foreignId('kegiatanposyandu_balita_id')->constrained('kegiatan_posyandus')->onDelete('cascade');
            $table->float('panjang_badan');
            $table->float('berat_badan');
            $table->float('lingkep_periksa');
            $table->integer('pemberian_asi_kuartal')->nullable();
            $table->integer('vitamin_kuartal')->nullable();
            $table->text('catatan');
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
        Schema::dropIfExists('periksa_balitas');
    }
};
