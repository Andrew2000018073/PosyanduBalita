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
        Schema::create('kegiatan_posyandus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('posyandu_id')->constrained('posyandus')->onDelete('cascade');
            $table->string('tgl_kegiatan');
            $table->enum('status_kegiatan', ['selesai', 'belum selesai']);
            $table->enum('tipe_kegiatan', ['Balita', 'WUS']);
            $table->text('detail');
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
        Schema::dropIfExists('kegiatan_posyandus');
    }
};
