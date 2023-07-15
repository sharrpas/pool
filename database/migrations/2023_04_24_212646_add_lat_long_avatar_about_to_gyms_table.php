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
        Schema::table('gyms', function (Blueprint $table) {
            $table->string('about')->nullable()->after('name');
            $table->string('avatar')->nullable()->after('address');
            $table->string('image')->nullable()->after('avatar');
            $table->string('lat')->nullable()->after('image');
            $table->string('long')->nullable()->after('lat');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gyms', function (Blueprint $table) {
            //
        });
    }
};
