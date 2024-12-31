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
        Schema::create('pivot_kontrasepsi_puses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kontrasepsi_id')->constrained('alat_kontrasepsis')->onDelete('cascade');
            $table->foreignId('pus_id')->constrained('p_u_s')->onDelete('cascade');
            $table->enum('status', ['Sedang dipakai', 'Sudah diganti']);
            $table->text('tanggal_pertama_pakai');
            $table->text('tanggal_berhenti_pakai')->nullable();
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
        Schema::dropIfExists('pivot_kontrasepsi_puses');
    }
};
