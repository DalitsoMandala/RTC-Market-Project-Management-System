<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubmissionTargetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        DB::unprepared("


INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(1, 1, 1, 'Total', 10000.00, '2025-01-31 11:50:59', '2025-01-31 11:50:59');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(2, 1, 3, 'Total (% Percentage)', 5.00, '2025-01-31 11:50:59', '2025-01-31 11:50:59');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(3, 1, 5, 'Total', 100000.00, '2025-01-31 11:50:59', '2025-01-31 11:50:59');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(4, 1, 6, 'Total (% Percentage)', 5.00, '2025-01-31 11:50:59', '2025-01-31 11:50:59');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(5, 1, 8, 'Total', 6.00, '2025-01-31 11:50:59', '2025-01-31 11:50:59');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(6, 1, 9, 'Total', 2.00, '2025-01-31 11:50:59', '2025-01-31 11:50:59');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(7, 1, 10, 'Total', 2.00, '2025-01-31 11:50:59', '2025-01-31 11:50:59');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(8, 1, 11, 'Total (% Percentage)', 5.00, '2025-01-31 11:50:59', '2025-01-31 11:50:59');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(9, 1, 12, 'Total', 1.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(10, 1, 13, 'Total', 3.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(11, 1, 14, 'Total', 3.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(12, 1, 15, 'Total', 1.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(13, 1, 16, 'Total', 5.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(14, 1, 17, 'Total', 2.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(15, 1, 18, 'Total', 107.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(16, 1, 19, 'Total (% Percentage)', 5.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(17, 1, 20, 'Cassava', 3000.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(18, 1, 20, 'Potato', 10.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(19, 1, 20, 'Sweet potato', 12000.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(20, 1, 21, 'Total', 6.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(21, 1, 22, 'Total', 1.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(22, 1, 23, 'Total (% Percentage)', 9.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(23, 1, 24, 'Total', 8.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(24, 1, 25, 'Total', 100.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(25, 1, 26, 'Total', 10000.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(26, 1, 27, 'Total', 6.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(27, 1, 28, 'Total', 12.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(28, 1, 29, 'Total', 50.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(29, 1, 30, 'Total', 180.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(30, 1, 31, 'Total', 10.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(31, 1, 32, 'Total', 30.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(32, 1, 33, 'Total (% Percentage)', 5.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(33, 1, 34, 'Total', 8.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(34, 1, 35, 'Total', 5.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(35, 1, 36, 'Total', 9.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(36, 1, 37, 'Total', 5.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(37, 1, 38, 'Total', 1.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(38, 1, 39, 'Total', 3.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(39, 1, 40, 'Total', 3000.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(40, 1, 41, 'Total', 8000.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(41, 1, 42, 'Total', 3.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(42, 1, 43, 'Total (% Percentage)', 5.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(43, 1, 44, 'Total', 3.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(44, 1, 45, 'Total', 2.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(45, 1, 46, 'Total', 6.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(46, 1, 47, 'Total', 2.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(47, 1, 48, 'Total', 6.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(48, 1, 49, 'Total', 6.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(49, 1, 50, 'Total', 6.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(50, 1, 51, 'Total', 3.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(51, 1, 52, 'Total', 1.00, '2025-01-31 11:51:00', '2025-01-31 11:51:00');

        ");
    }
}
