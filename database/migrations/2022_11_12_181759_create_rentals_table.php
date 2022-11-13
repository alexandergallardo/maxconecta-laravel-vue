<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRentalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                ->nullable()
                ->constrained('clients')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('movie_id')
                ->nullable()
                ->constrained('movies')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->date('delivery');
            $table->date('entry')->nullable();;
            $table->text('description')->nullable();

            $table->engine = 'InnoDB';
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
        Schema::dropIfExists('rentals');
    }
}
