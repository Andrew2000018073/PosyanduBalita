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
        //
        Schema::create('Pendaftaran_Ibu_Hamils', function (Blueprint $table) {
            $table->id();
            $table->integer('kehamilan_ke');
            $table->string('tanggal_awal_hamil');
            $table->string('tanggal_daftar');
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
        //
    }
};
