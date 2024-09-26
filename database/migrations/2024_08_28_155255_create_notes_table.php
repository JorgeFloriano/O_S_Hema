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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id');
            $table->string('equip_mod')->default('Não identificado');
            $table->string('equip_id')->default('Não identificado');
            $table->string('equip_type')->default('Não identificado');
            $table->string('situation');
            $table->string('cause')->default('Não identificada');
            $table->text('services');
            $table->date('date');
            $table->time('go_start');
            $table->time('go_end');
            $table->time('start');
            $table->time('end');
            $table->time('back_start');
            $table->time('back_end');
            $table->float('food', 8, 2)->default(0);
            $table->float('km_start', 8, 2)->default(0);
            $table->float('km_end', 8, 2)->default(0);
            $table->float('expense', 8, 2)->default(0);
            $table->string('obs')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
