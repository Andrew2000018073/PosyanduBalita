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
        Schema::create('imunisasipvbalita', function (Blueprint $table) {
            $table->id();
            $table->foreignId('imunisasi_id')->constrained('imunisasi_balitas')->onDelete('cascade');
            $table->foreignId('periksabalita_id')->constrained('periksa_balitas')->onDelete('cascade');
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
        Schema::dropIfExists('imunisasipvbalita');
    }
};
