<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Source;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [     'person_id' => 1,
                'form_id' => 1
            ],
            [
                'person_id' => 1,
                'form_id' => 2
            ],
            [
                'person_id' => 1,
                'form_id' => 3
            ],
            [
                'person_id' => 2,
                'form_id' => 1
            ],
            [
                'person_id' => 2,
                'form_id' => 2
            ],
            [
                'person_id' => 2,
                'form_id' => 3
            ],
            [
                'person_id' => 3,
                'form_id' => 1
            ],
            [
                'person_id' => 3,
                'form_id' => 2
            ],
            [
                'person_id' => 3,
                'form_id' => 3
            ],
            [
                'person_id' => 4,
                'form_id' => 1
            ],
            [
                'person_id' => 4,
                'form_id' => 2
            ],
            [
                'person_id' => 4,
                'form_id' => 3
            ],
            [
                'person_id' => 5,
                'form_id' => 2
            ],
            [
                'person_id' => 5,
                'form_id' => 3
            ],
            [
                'person_id' => 6,
                'form_id' => 2
            ],
            [
                'person_id' => 6,
                'form_id' => 3
            ],
            [
                'person_id' => 7,
                'form_id' => 2
            ],
            [
                'person_id' => 7,
                'form_id' => 3
            ],
            [
                'person_id' => 8,
                'form_id' => 2
            ],
            [
                'person_id' => 8,
                'form_id' => 3
            ],
            [
                'person_id' => 10,
                'form_id' => 6
            ],
            [
                'person_id' => 9,
                'form_id' => 6
            ],
            [
                'person_id' => 11,
                'form_id' => 6
            ],
            [
                'person_id' => 12,
                'form_id' => 1
            ],
            [
                'person_id' => 12,
                'form_id' => 4
            ],
            [
                'person_id' => 13,
                'form_id' => 1
            ],
            [
                'person_id' => 13,
                'form_id' => 4
            ],
            [
                'person_id' => 14,
                'form_id' => 1
            ],
            [
                'person_id' => 14,
                'form_id' => 4
            ],
            [
                'person_id' => 15,
                'form_id' => 2
            ],
            [
                'person_id' => 15,
                'form_id' => 3
            ],
            [
                'person_id' => 16,
                'form_id' => 2
            ],
            [
                'person_id' => 16,
                'form_id' => 3
            ],
            [
                'person_id' => 17,
                'form_id' => 2
            ],
            [
                'person_id' => 17,
                'form_id' => 3
            ],
            [
                'person_id' => 18,
                'form_id' => 6
            ],
            [
                'person_id' => 19,
                'form_id' => 6
            ],
            [
                'person_id' => 20,
                'form_id' => 6
            ],
            [
                'person_id' => 21,
                'form_id' => 6
            ],
            [
                'person_id' => 22,
                'form_id' => 6
            ],
            [
                'person_id' => 23,
                'form_id' => 6
            ],
            [
                'person_id' => 24,
                'form_id' => 6
            ],
            [
                'person_id' => 25,
                'form_id' => 6
            ],
            [
                'person_id' => 26,
                'form_id' => 6
            ],
            [
                'person_id' => 27,
                'form_id' => 6
            ],
            [
                'person_id' => 28,
                'form_id' => 6
            ],
            [
                'person_id' => 29,
                'form_id' => 6
            ],
            [
                'person_id' => 30,
                'form_id' => 6
            ],
            [
                'person_id' => 31,
                'form_id' => 6
            ],
            [
                'person_id' => 32,
                'form_id' => 6
            ],
            [
                'person_id' => 33,
                'form_id' => 6
            ],
            [
                'person_id' => 34,
                'form_id' => 6
            ],
            [
                'person_id' => 35,
                'form_id' => 6
            ],
            [
                'person_id' => 36,
                'form_id' => 2
            ],
            [
                'person_id' => 36,
                'form_id' => 3
            ],
            [
                'person_id' => 37,
                'form_id' => 2
            ],
            [
                'person_id' => 37,
                'form_id' => 3
            ],
            [
                'person_id' => 38,
                'form_id' => 2
            ],
            [
                'person_id' => 38,
                'form_id' => 3
            ],
            [
                'person_id' => 39,
                'form_id' => 2
            ],
            [
                'person_id' => 39,
                'form_id' => 3
            ],
            [
                'person_id' => 40,
                'form_id' => 2
            ],
            [
                'person_id' => 40,
                'form_id' => 3
            ],
            [
                'person_id' => 41,
                'form_id' => 2
            ],
            [
                'person_id' => 41,
                'form_id' => 3
            ],
            [
                'person_id' => 42,
                'form_id' => 2
            ],
            [
                'person_id' => 42,
                'form_id' => 3
            ],
            [
                'person_id' => 43,
                'form_id' => 2
            ],
            [
                'person_id' => 43,
                'form_id' => 3
            ],
            [
                'person_id' => 44,
                'form_id' => 6
            ],
            [
                'person_id' => 45,
                'form_id' => 6
            ],
            [
                'person_id' => 46,
                'form_id' => 6
            ],
            [
                'person_id' => 47,
                'form_id' => 6
            ],
            [
                'person_id' => 48,
                'form_id' => 6
            ],
            [
                'person_id' => 49,
                'form_id' => 6
            ],
            [
                'person_id' => 50,
                'form_id' => 6
            ],
            [
                'person_id' => 51,
                'form_id' => 6
            ],
            [
                'person_id' => 54,
                'form_id' => 6
            ],
            [
                'person_id' => 55,
                'form_id' => 6
            ],
            [
                'person_id' => 56,
                'form_id' => 6
            ],
            [
                'person_id' => 57,
                'form_id' => 6
            ],
            [
                'person_id' => 58,
                'form_id' => 2
            ],
            [
                'person_id' => 58,
                'form_id' => 3
            ],
            [
                'person_id' => 59,
                'form_id' => 2
            ],
            [
                'person_id' => 59,
                'form_id' => 3
            ],
            [
                'person_id' => 60,
                'form_id' => 2
            ],
            [
                'person_id' => 60,
                'form_id' => 3
            ],
            [
                'person_id' => 61,
                'form_id' => 2
            ],
            [
                'person_id' => 61,
                'form_id' => 3
            ],
            [
                'person_id' => 62,
                'form_id' => 5
            ],
            [
                'person_id' => 63,
                'form_id' => 5
            ],
            [
                'person_id' => 64,
                'form_id' => 5
            ],
            [
                'person_id' => 65,
                'form_id' => 5
            ],
            [
                'person_id' => 66,
                'form_id' => 5
            ],
            [
                'person_id' => 67,
                'form_id' => 6
            ],
            [
                'person_id' => 68,
                'form_id' => 6
            ],
            [
                'person_id' => 69,
                'form_id' => 2
            ],
            [
                'person_id' => 69,
                'form_id' => 3
            ],
            [
                'person_id' => 70,
                'form_id' => 2
            ],
            [
                'person_id' => 70,
                'form_id' => 3
            ],
            [
                'person_id' => 71,
                'form_id' => 2
            ],
            [
                'person_id' => 71,
                'form_id' => 3
            ],
            [
                'person_id' => 72,
                'form_id' => 6
            ],
            [
                'person_id' => 73,
                'form_id' => 6
            ],
            [
                'person_id' => 74,
                'form_id' => 6
            ],
            [
                'person_id' => 76,
                'form_id' => 6
            ],
            [
                'person_id' => 79,
                'form_id' => 6
            ],
            [
                'person_id' => 80,
                'form_id' => 6
            ],
            [
                'person_id' => 81,
                'form_id' => 6
            ],
            [
                'person_id' => 82,
                'form_id' => 1
            ],
            [
                'person_id' => 83,
                'form_id' => 1
            ],
            [
                'person_id' => 84,
                'form_id' => 1
            ],
            [
                'person_id' => 85,
                'form_id' => 1
            ],
            [
                'person_id' => 86,
                'form_id' => 1
            ],
            [
                'person_id' => 87,
                'form_id' => 1
            ],
            [
                'person_id' => 88,
                'form_id' => 6
            ],
            [
                'person_id' => 89,
                'form_id' => 6
            ],
            [
                'person_id' => 90,
                'form_id' => 6
            ],
            [
                'person_id' => 91,
                'form_id' => 6
            ],
            [
                'person_id' => 92,
                'form_id' => 6
            ],
            [
                'person_id' => 93,
                'form_id' => 6
            ],
            [
                'person_id' => 94,
                'form_id' => 6
            ],
            [
                'person_id' => 95,
                'form_id' => 6
            ],
            [
                'person_id' => 96,
                'form_id' => 6
            ],
            [
                'person_id' => 97,
                'form_id' => 6
            ],
            [
                'person_id' => 100,
                'form_id' => 6
            ],
            [
                'person_id' => 101,
                'form_id' => 6
            ],
            [
                'person_id' => 102,
                'form_id' => 6
            ],
            [
                'person_id' => 103,
                'form_id' => 6
            ],
            [
                'person_id' => 104,
                'form_id' => 6
            ],
            [
                'person_id' => 105,
                'form_id' => 6
            ],
            [
                'person_id' => 75,
                'form_id' => 6
            ],
            [
                'person_id' => 77,
                'form_id' => 6
            ],
            [
                'person_id' => 78,
                'form_id' => 6
            ],
            [
                'person_id' => 99,
                'form_id' => 6
            ],
            [
                'person_id' => 98,
                'form_id' => 6
            ],
            [
                'person_id' => 53,
                'form_id' => 6
            ],
            [
                'person_id' => 52,
                'form_id' => 6
            ],
        ];

        Source::insert($data);
    }
}