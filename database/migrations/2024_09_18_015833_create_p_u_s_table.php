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
        Schema::create('p_u_s', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wus_id')->constrained('wuses')->onDelete('cascade');
            $table->string('nama_suami');
            $table->integer('jumlah_anak_hidup');
            $table->float('lingkar_perut_suami');
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
        Schema::dropIfExists('p_u_s');
    }
};
