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
        Schema::create('periksawuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wus_id')->nullable()->constrained('wuses')->onDelete('cascade');
            $table->foreignId('kegiatanposyandu_w_u_s_id')->constrained('kegiatan_posyandus')->onDelete('cascade');
            $table->integer('tablettambah_darahs_kuartal')->nullable();
            $table->integer('imunisasi_kehamilans_kuartal')->nullable();
            $table->integer('vitamin_kuartal')->nullable();
            $table->float('lila_periksa');
            $table->float('lingkarperut_periksa');
            $table->integer('diastol');
            $table->integer('sistol');
            $table->enum('statusperiksa', ['Tidak menikah', 'Hamil', 'Tidak hamil & Menyusui', 'Tidak hamil & Tidak menyusui', 'Nifas']);
            $table->float('tinggi_fundus')->nullable();
            // $table->float('hasil_penimbangan');
            $table->text('keluhan');
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
        Schema::dropIfExists('periksawuses');
    }
};
