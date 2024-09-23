<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Codec\TimestampLastCombCodec;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('sector');
            $table->smallInteger('client_id');
            $table->smallInteger('adm_id');
            $table->smallInteger('tec_id')->nullable();
            $table->boolean('finished')->default(0);
            $table->string('equipment')->nullable();
            $table->string('req_name');
            $table->date('req_date')->useCurrent();
            $table->time('req_time')->useCurrent();
            $table->text('req_descr');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
