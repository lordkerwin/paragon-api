<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id');
            $table->timestamp('start');
            $table->timestamp('end');
            $table->decimal('pay_rate', 11, 2)->default(0);
            $table->decimal('duration', 11, 2)->default(0);
            $table->decimal('value', 11, 2)->default(0);
            $table->foreignId('shift_status_id');
            $table->boolean('processed')->default(false);
            $table->boolean('payroll_locked')->default(false);
            $table->softDeletes();
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
        Schema::dropIfExists('shifts');
    }
}
