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
        Schema::create('bayis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pus_id')->constrained('p_u_s')->onDelete('cascade');
            $table->foreignId('posyandus_id')->constrained('posyandus')->onDelete('cascade');
            // $table->foreignId('imunisasibayis_id')->constrained('');
            $table->string('namabalita');
            $table->string('tanggal_lahir');
            $table->float('beratbadan_lahir');
            $table->float('panjangbadan_lahir');
            $table->float('likep_lahir');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
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
        Schema::dropIfExists('bayis');
    }
};
