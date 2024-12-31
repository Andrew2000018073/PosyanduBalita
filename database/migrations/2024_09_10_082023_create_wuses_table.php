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
        Schema::create('wuses', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('tanggal_lahir');
            $table->enum('statuswus', ['Tidak menikah', 'Hamil', 'Tidak hamil & Menyusui', 'Tidak hamil & Tidak menyusui', 'Nifas']);
            $table->float('lila_wus');
            // $table->foreignId('kehamilan_id')->nullable()->constrained('kehamilans')->onDelete('cascade');
            $table->foreignId('posyandus_id')->constrained('posyandus')->onDelete('cascade');
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
        Schema::dropIfExists('wuses');
    }
};
