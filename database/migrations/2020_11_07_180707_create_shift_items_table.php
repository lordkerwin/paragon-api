<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id');
            $table->timestamp('start');
            $table->timestamp('end');
            $table->decimal('duration', 11, 2)->default(0);
            $table->decimal('value', 11, 2)->default(0);
            $table->foreignId('shift_item_type_id');
            $table->boolean('processed')->default(false);
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
        Schema::dropIfExists('shift_items');
    }
}
