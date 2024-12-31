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
        Schema::create('kehamilans', function (Blueprint $table) {
            $table->id();
            $table->integer('kehamilan_ke')->nullable();
            $table->string('tanggal_awal_hamil')->nullable();
            $table->string('tanggal_daftar');
            // $table->foreignId('pendaftaran_ibu_hamil_id')->constrained('Pendaftaran_Ibu_Hamils');
            $table->foreignId('wus_id')->constrained('wuses')->onDelete('cascade');
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
        Schema::dropIfExists('kehamilans');
    }
};
