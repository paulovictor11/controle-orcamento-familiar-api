<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert(['name' => 'Outras']);
        DB::table('categories')->insert(['name' => 'Alimentação']);
        DB::table('categories')->insert(['name' => 'Saúde']);
        DB::table('categories')->insert(['name' => 'Moradia']);
        DB::table('categories')->insert(['name' => 'Transporte']);
        DB::table('categories')->insert(['name' => 'Educação']);
        DB::table('categories')->insert(['name' => 'Lazer']);
        DB::table('categories')->insert(['name' => 'Imprevistos']);
    }
}
