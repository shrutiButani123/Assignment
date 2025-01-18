<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Country;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('countries')->delete();
        $countries = array(
            array('id' => 1,'code' => 'AF' ,'name' => "Afghanistan"),
            array('id' => 2,'code' => 'AL' ,'name' => "Albania"),
            array('id' => 3,'code' => 'DZ' ,'name' => "Algeria"),
            array('id' => 4,'code' => 'AS' ,'name' => "American Samoa"),
            array('id' => 5,'code' => 'AD' ,'name' => "Andorra"),
            array('id' => 6,'code' => 'AO' ,'name' => "Angola"),
            array('id' => 7,'code' => 'AI' ,'name' => "Anguilla"),
            array('id' => 8,'code' => 'AQ' ,'name' => "Antarctica"),
            array('id' => 9,'code' => 'AG' ,'name' => "Antigua And Barbuda"),
            array('id' => 10,'code' => 'AR','name' => "Argentina"),
            array('id' => 11,'code' => 'IN','name' => "India"),
            );
        DB::table('countries')->insert($countries);
    }
}
