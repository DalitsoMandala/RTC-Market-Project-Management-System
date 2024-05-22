<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

<<<<<<< HEAD
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
$this->call(UserSeeder::class);
        $this->call(SystemSeeder::class);
        $this->call(CgiarProjectSeeder::class);
        $this->call(ProjectSeeder::class);
        $this->call(IndicatorSeeder::class);
        $this->call(FormSeeder::class);
    }
}
=======
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
>>>>>>> d6fad409f601ae8845590b63149d156bb36769e8
