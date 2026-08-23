<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Sucursal;
use App\Models\User;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
      
      Categoria::factory(100)->create();

      User::create([

        'name'=> 'Lisandro Villegas',
        'email'=> 'lisandrovillegas1@gmail.com',
        'password'=> bcrypt('12345678'),

      ]);

    

    }
}
