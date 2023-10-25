<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('amenity_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('amenity_id');
            $table->date('request_date');
            $table->time('preffered_time');
            $table->longText('remark');
            $table->integer('no_of_person');
            $table->string('status')->default('pending');
            $table->string('amount')->nullable();
            $table->dateTime('date_completed')->nullable();
            $table->longText('visitors')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amenity_requests');
    }
};
