<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('mandor_panen', function (Blueprint $table) {
            $table->id();
            $table->string('bulan');
            $table->string('kd_afd_bagian');
            $table->string('plant');
            $table->string('reg_mb');
            $table->string('regmdr');
            $table->string('regmdr_sap')->nullable();
            $table->string('status');
            $table->string('nama');
            $table->string('jabatan');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mandor_panen');
    }
};