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

        $forms = [
            1 => 'HOUSEHOLD CONSUMPTION FORM',
            2 => 'RTC PRODUCTION AND MARKETING FORM FARMERS',
            3 => 'RTC PRODUCTION AND MARKETING FORM PROCESSORS',
            4 => 'SCHOOL RTC CONSUMPTION FORM',
            5 => 'ATTENDANCE REGISTER',
            6 => 'REPORT FORM',

        ];

        $entries = [
            [
                'person_id' => 2,
                'form_id' => $forms[1
                ]
            ],
            [
                'person_id' => 2,
                'form_id' => $forms[2
                ]
            ],
            [
                'person_id' => 2,
                'form_id' => $forms[3
                ]
            ],
            [
                'person_id' => 1,
                'form_id' => $forms[1
                ]
            ],
            [
                'person_id' => 1,
                'form_id' => $forms[2
                ]
            ],
            [
                'person_id' => 1,
                'form_id' => $forms[3
                ]
            ],
            [
                'person_id' => 3,
                'form_id' => $forms[1
                ]
            ],
            [
                'person_id' => 3,
                'form_id' => $forms[2
                ]
            ],
            [
                'person_id' => 3,
                'form_id' => $forms[3
                ]
            ],
            [
                'person_id' => 3,
                'form_id' => $forms[5
                ]
            ], //added some attendance
            [
                'person_id' => 4,
                'form_id' => $forms[1
                ]
            ],
            [
                'person_id' => 4,
                'form_id' => $forms[2
                ]
            ],
            [
                'person_id' => 4,
                'form_id' => $forms[3
                ]
            ],
            [
                'person_id' => 5,
                'form_id' => $forms[1
                ]
            ],
            [
                'person_id' => 5,
                'form_id' => $forms[2
                ]
            ],
            [
                'person_id' => 5,
                'form_id' => $forms[3
                ]
            ],
            [
                'person_id' => 6,
                'form_id' => $forms[1
                ]
            ],
            [
                'person_id' => 6,
                'form_id' => $forms[2
                ]
            ],
            [
                'person_id' => 6,
                'form_id' => $forms[3
                ]
            ],
            [
                'person_id' => 7,
                'form_id' => $forms[1
                ]
            ],
            [
                'person_id' => 7,
                'form_id' => $forms[2
                ]
            ],
            [
                'person_id' => 7,
                'form_id' => $forms[3
                ]
            ],
            [
                'person_id' => 8,
                'form_id' => $forms[1
                ]
            ],
            [
                'person_id' => 8,
                'form_id' => $forms[2
                ]
            ],
            [
                'person_id' => 8,
                'form_id' => $forms[3
                ]
            ],
            [
                'person_id' => 9,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 10,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 11,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 12,
                'form_id' => $forms[1
                ]
            ],
            [
                'person_id' => 12,
                'form_id' => $forms[4
                ]
            ],
            [
                'person_id' => 13,
                'form_id' => $forms[1
                ]
            ],
            [
                'person_id' => 13,
                'form_id' => $forms[4
                ]
            ],
            [
                'person_id' => 14,
                'form_id' => $forms[1
                ]
            ],
            [
                'person_id' => 14,
                'form_id' => $forms[4
                ]
            ],
            [
                'person_id' => 15,
                'form_id' => $forms[2
                ]
            ],
            [
                'person_id' => 15,
                'form_id' => $forms[3
                ]
            ],
            [
                'person_id' => 16,
                'form_id' => $forms[2
                ]
            ],
            [
                'person_id' => 16,
                'form_id' => $forms[3
                ]
            ],
            [
                'person_id' => 17,
                'form_id' => $forms[2
                ]
            ],
            [
                'person_id' => 17,
                'form_id' => $forms[3
                ]
            ],
            [
                'person_id' => 18,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 19,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 20,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 21,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 22,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 23,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 24,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 25,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 26,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 27,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 28,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 29,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 30,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 31,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 32,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 33,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 34,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 35,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 36,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 37,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 38,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 39,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 40,
                'form_id' => $forms[2
                ]
            ],
            [
                'person_id' => 40,
                'form_id' => $forms[3
                ]
            ],
            [
                'person_id' => 41,
                'form_id' => $forms[2
                ]
            ],
            [
                'person_id' => 41,
                'form_id' => $forms[3
                ]
            ],
            [
                'person_id' => 42,
                'form_id' => $forms[2
                ]
            ],
            [
                'person_id' => 42,
                'form_id' => $forms[3
                ]
            ],
            [
                'person_id' => 43,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 44,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 45,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 46,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 47,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 48,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 49,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 50,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 51,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 52,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 53,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 54,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 55,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 56,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 57,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 58,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 59,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 60,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 61,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 62,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 63,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 64,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 65,
                'form_id' => $forms[2
                ]
            ],
            [
                'person_id' => 65,
                'form_id' => $forms[3
                ]
            ],
            [
                'person_id' => 66,
                'form_id' => $forms[2
                ]
            ],
            [
                'person_id' => 66,
                'form_id' => $forms[3
                ]
            ],
            [
                'person_id' => 67,
                'form_id' => $forms[2
                ]
            ],
            [
                'person_id' => 67,
                'form_id' => $forms[3
                ]
            ],
            [
                'person_id' => 68,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 69,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 70,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 71,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 72,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 73,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 74,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 75,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 76,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 77,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 78,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 79,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 80,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 81,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 82,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 83,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 84,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 85,
                'form_id' => $forms[1
                ]
            ],
            [
                'person_id' => 86,
                'form_id' => $forms[1
                ]
            ],
            [
                'person_id' => 87,
                'form_id' => $forms[1
                ]
            ],
            [
                'person_id' => 88,
                'form_id' => $forms[1
                ]
            ],
            [
                'person_id' => 89,
                'form_id' => $forms[1
                ]
            ],
            [
                'person_id' => 90,
                'form_id' => $forms[1
                ]
            ],
            [
                'person_id' => 91,
                'form_id' => $forms[1
                ]
            ],
            [
                'person_id' => 92,
                'form_id' => $forms[1
                ]
            ],
            [
                'person_id' => 93,
                'form_id' => $forms[1
                ]
            ],
            [
                'person_id' => 94,
                'form_id' => $forms[1
                ]
            ],
            [
                'person_id' => 95,
                'form_id' => $forms[1
                ]
            ],
            [
                'person_id' => 96,
                'form_id' => $forms[1
                ]
            ],
            [
                'person_id' => 97,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 98,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 99,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 100,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 101,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 102,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 103,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 104,
                'form_id' => $forms[6
                ]
            ],
            [
                'person_id' => 105,
                'form_id' => $forms[6
                ]
            ],
        ];

        // foreach ($entries as $entry) {
        //     Source::create($entry);
        // }

        DB::statement("
      INSERT INTO cdms.sources(person_id, form_id) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 1),
(2, 2),
(2, 3),
(3, 1),
(3, 2),
(3, 3),
(4, 1),
(4, 2),
(4, 3),
(5, 2),
(5, 3),
(6, 2),
(6, 3),
(7, 2),
(7, 3),
(8, 2),
(8, 3),
(10, 6),
(9, 6),
(11, 6),
(12, 1),
(12, 4),
(13, 1),
(13, 4),
(14, 1),
(14, 4),
(15, 2),
(15, 3),
(16, 2),
(16, 3),
(17, 2),
(17, 3),
(18, 6),
(19, 6),
(20, 6),
(21, 6),
(22, 6),
(23, 6),
(24, 6),
(25, 6),
(26, 6),
(27, 6),
(28, 6),
(29, 6),
(30, 6),
(31, 6),
(32, 6),
(33, 6),
(34, 6),
(35, 6),
(36, 2),
(36, 3),
(37, 2),
(37, 3),
(38, 2),
(38, 3),
(39, 2),
(39, 3),
(40, 2),
(40, 3),
(41, 2),
(41, 3),
(42, 2),
(42, 3),
(43, 2),
(43, 3),
(44, 6),
(45, 6),
(46, 6),
(47, 6),
(48, 6),
(49, 6),
(50, 6),
(51, 6),
(54, 6),
(55, 6),
(56, 6),
(57, 6),
(58, 2),
(58, 3),
(59, 2),
(59, 3),
(60, 2),
(60, 3),
(61, 2),
(61, 3),
(62, 5),
(63, 5),
(64, 5),
(65, 5),
(66, 5),
(67, 6),
(68, 6),
(69, 2),
(69, 3),
(70, 2),
(70, 3),
(71, 2),
(71, 3),
(72, 6),
(73, 6),
(74, 6),
(76, 6),
(79, 6),
(80, 6),
(81, 6),
(82, 1),
(83, 1),
(84, 1),
(85, 1),
(86, 1),
(87, 1),
(88, 6),
(89, 6),
(90, 6),
(91, 6),
(92, 6),
(93, 6),
(94, 6),
(95, 6),
(96, 6),
(97, 6),
(100, 6),
(101, 6),
(102, 6),
(103, 6),
(104, 6),
(105, 6),
(75, 6),
(77, 6),
(78, 6),
(99, 6),
(98, 6),
(53, 6),
(52, 6);
    ");

    }
}
