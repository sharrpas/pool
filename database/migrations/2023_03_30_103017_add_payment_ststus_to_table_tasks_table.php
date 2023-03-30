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
        Schema::table('table_tasks', function (Blueprint $table) {
            $table->enum('payment_status', ['paid', 'unpaid', 'pending'])->after('price_so_far')->default('unpaid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('table_tasks', function (Blueprint $table) {
            //
        });
    }
};
