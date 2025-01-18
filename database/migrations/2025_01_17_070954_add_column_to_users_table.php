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
        Schema::table('users', function (Blueprint $table) {
            $table->string('last_name')->after('name');
            $table->string('contact')->after('password');
            $table->integer('city_id')->after('contact')->nullable();
            $table->integer('state_id')->after('city_id')->nullable();
            $table->integer('country_id')->after('state_id')->nullable();
            $table->string('postcode')->after('country_id')->nullable();
            $table->text('hobbies')->after('postcode')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->after('hobbies');           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('last_name');
            $table->string('contact');
            $table->integer('city_id');
            $table->integer('state_id');
            $table->integer('country_id');
            $table->string('postcode');
            $table->text('hobbies');
            $table->enum('gender', ['male', 'female', 'other']);
        });
    }
};
