<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class VesselTrack extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('vessel_track', function (Blueprint $table) {
            $table->id();
            $table->string('mmsi', 100);
            $table->string('status', 25)->nullable();
            $table->integer('station')->nullable();
            $table->decimal('speed', 5, 3)->default(0.0)->nullable();
            $table->decimal('lon', 15, 8)->default(0.0)->nullable();
            $table->decimal('lat', 15, 8)->default(0.0)->nullable();
            $table->string('course', 50)->nullable();
            $table->string('heading', 50)->nullable();
            $table->string('rot', 50)->nullable();
            $table->timestamp('created_at'); 
            $table->timestamp('updated_at');   
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vessel_track');
    }
}
