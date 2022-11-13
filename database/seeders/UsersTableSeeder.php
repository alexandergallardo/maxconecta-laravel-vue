<?php


namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();

        DB::table('users')->insert([
            'name' => 'Alexander Gallardo',
            'username' => 'admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
            'type' => 'Administrador',
        ]);

        DB::table('users')->insert([
            'name' => 'Usuario Encargado',
            'username' => 'encargado',
            'email' => 'encargado@encargado.com',
            'password' => bcrypt('password'),
            'type' => 'Encargado',
        ]);
    }
}
