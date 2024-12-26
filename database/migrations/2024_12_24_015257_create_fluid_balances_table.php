<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fluid_balances', function (Blueprint $table) {
            $table->id();
            $table->string('no_rm');
            $table->string('pasien');
            $table->string('bb');
            $table->string('usia');
            $table->string('suhu_badan');
            $table->string('infus'); // Cairan infus
            $table->string('transfusi_darah'); // Cairan Transfusi darah
            $table->string('terapi'); // Cairan terapi drip injeksi
            $table->string('makan_minum_ngt'); // Cairan Makan minum melalui NGT
            
            $table->string('urin'); // Urine per 24 jam
            $table->string('bab'); // BAB per 24 jam
            $table->string('muntah'); // Muntah
            $table->string('cairan_ngt')->nullable(); // Cairan keluar melalui NGT
            $table->string('drainage')->nullable(); // Drainage per 24 jam
            $table->string('perdarahan')->nullable(); // Perdarahan per 24 jam
            
            $table->string('cairan_masuk'); // Input cairan masuk
            $table->string('cairan_keluar'); // Cairan keluar total
            $table->string('iwl'); // IWL (Invisible Water Loss)
            $table->string('air_metabolisme'); // Air metabolisme
            $table->string('balance_cairan'); // Resulting balance cairan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fluid_balances');
    }
};
