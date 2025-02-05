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
(2, 1, 3, 'Total (% Percentage)', 5.00, '2025-01-31 09:50:59', '2025-01-31 09:50:59');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(3, 1, 5, 'Total', 100000.00, '2025-01-31 09:50:59', '2025-01-31 09:50:59');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(4, 1, 6, 'Total (% Percentage)', 5.00, '2025-01-31 09:50:59', '2025-01-31 09:50:59');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(5, 1, 8, 'Total', 6.00, '2025-01-31 09:50:59', '2025-01-31 09:50:59');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(6, 1, 9, 'Total', 2.00, '2025-01-31 09:50:59', '2025-01-31 09:50:59');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(7, 1, 10, 'Total', 2.00, '2025-01-31 09:50:59', '2025-01-31 09:50:59');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(8, 1, 11, 'Total (% Percentage)', 5.00, '2025-01-31 09:50:59', '2025-01-31 09:50:59');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(9, 1, 12, 'Total', 1.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(10, 1, 13, 'Total', 3.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(11, 1, 14, 'Total', 3.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(12, 1, 15, 'Total', 1.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(13, 1, 16, 'Total', 5.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(14, 1, 17, 'Total', 2.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(15, 1, 18, 'Total', 107.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(16, 1, 19, 'Total (% Percentage)', 5.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(17, 1, 20, 'Cassava', 3000.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(18, 1, 20, 'Potato', 10.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(19, 1, 20, 'Sweet potato', 12000.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(20, 1, 21, 'Total', 6.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(21, 1, 22, 'Total', 1.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(22, 1, 23, 'Total (% Percentage)', 9.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(23, 1, 24, 'Total', 8.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(24, 1, 25, 'Total', 100.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(25, 1, 26, 'Total', 10000.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(26, 1, 27, 'Total', 6.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(27, 1, 28, 'Total', 12.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(28, 1, 29, 'Total', 50.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(29, 1, 30, 'Total', 180.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(30, 1, 31, 'Total', 10.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(31, 1, 32, 'Total', 30.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(32, 1, 33, 'Total (% Percentage)', 5.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(33, 1, 34, 'Total', 8.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(34, 1, 35, 'Total', 5.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(35, 1, 36, 'Total', 9.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(36, 1, 37, 'Total', 5.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(37, 1, 38, 'Total', 1.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(38, 1, 39, 'Total', 3.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(39, 1, 40, 'Total', 3000.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(40, 1, 41, 'Total', 8000.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(41, 1, 42, 'Total', 3.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(42, 1, 43, 'Total (% Percentage)', 5.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(43, 1, 44, 'Total', 3.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(44, 1, 45, 'Total', 2.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(45, 1, 46, 'Total', 6.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(46, 1, 47, 'Total', 2.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(47, 1, 48, 'Total', 6.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(48, 1, 49, 'Total', 6.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(49, 1, 50, 'Total', 6.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(50, 1, 51, 'Total', 3.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(51, 1, 52, 'Total', 1.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(52, 1, 2, 'Total (% Percentage)', 5.00, '2025-02-03 11:01:50', '2025-02-03 11:01:50');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(53, 1, 4, 'Total (% Percentage)', 0.00, '2025-02-03 11:01:50', '2025-02-03 11:01:50');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(54, 1, 7, 'Total (% Percentage)', 0.00, '2025-02-03 11:01:50', '2025-02-03 11:01:50');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(60, 1, 1, 'Total', 10000.00, '2025-02-04 13:53:30', '2025-02-04 13:53:30');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(61, 2, 1, 'Total', 15000.00, '2025-02-04 14:01:51', '2025-02-04 14:01:51');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(63, 2, 2, 'Total (% Percentage)', 10.00, '2025-02-04 14:05:47', '2025-02-04 14:05:47');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(65, 2, 4, 'Total (% Percentage)', 5.00, '2025-02-04 14:08:49', '2025-02-04 14:08:49');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(66, 2, 3, 'Total (% Percentage)', 3.00, '2025-02-04 14:09:22', '2025-02-04 14:09:22');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(67, 2, 5, 'Total', 250000.00, '2025-02-04 14:10:09', '2025-02-04 14:10:09');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(69, 2, 6, 'Total (% Percentage)', 10.00, '2025-02-04 14:13:34', '2025-02-04 14:13:34');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(70, 2, 7, 'Total (% Percentage)', 5.00, '2025-02-04 14:14:04', '2025-02-04 14:14:04');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(71, 2, 8, 'Total', 6.00, '2025-02-04 14:14:34', '2025-02-04 14:14:34');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(72, 2, 9, 'Total', 3.00, '2025-02-04 14:15:24', '2025-02-04 14:15:24');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(73, 2, 10, 'Total', 3.00, '2025-02-04 14:15:41', '2025-02-04 14:15:41');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(74, 2, 11, 'Total (% Percentage)', 10.00, '2025-02-04 14:18:31', '2025-02-04 14:18:31');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(75, 2, 12, 'Total', 1.00, '2025-02-04 14:18:46', '2025-02-04 14:18:46');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(76, 2, 13, 'Total', 1.00, '2025-02-04 14:19:53', '2025-02-04 14:19:53');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(77, 2, 14, 'Total', 2.00, '2025-02-04 14:43:54', '2025-02-04 14:43:54');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(78, 2, 15, 'Total', 2.00, '2025-02-04 14:44:05', '2025-02-04 14:44:05');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(79, 2, 16, 'Total', 6.00, '2025-02-04 14:44:20', '2025-02-04 14:44:20');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(80, 2, 17, 'Total', 2.00, '2025-02-04 14:45:06', '2025-02-04 14:45:06');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(81, 2, 18, 'Total', 130.00, '2025-02-04 14:45:34', '2025-02-04 14:45:34');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(82, 2, 19, 'Total (% Percentage)', 10.00, '2025-02-04 14:46:11', '2025-02-04 14:46:11');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(83, 2, 20, 'Potato', 20.00, '2025-02-04 14:46:53', '2025-02-04 14:46:53');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(84, 2, 20, 'Sweet potato', 20000.00, '2025-02-04 14:46:53', '2025-02-04 14:46:53');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(85, 2, 20, 'Cassava', 5000.00, '2025-02-04 14:46:53', '2025-02-04 14:46:53');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(86, 2, 21, 'Total', 6.00, '2025-02-04 14:47:54', '2025-02-04 14:47:54');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(87, 2, 22, 'Total', 1.00, '2025-02-04 14:50:42', '2025-02-04 14:50:42');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(88, 2, 23, 'Total (% Percentage)', 9.00, '2025-02-04 14:51:53', '2025-02-04 14:51:53');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(89, 2, 24, 'Total', 9.00, '2025-02-04 14:52:13', '2025-02-04 14:52:13');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(90, 2, 25, 'Total', 150.00, '2025-02-04 14:52:30', '2025-02-04 14:52:30');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(91, 2, 26, 'Total', 10000.00, '2025-02-04 14:52:49', '2025-02-04 14:52:49');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(92, 2, 27, 'Total', 6.00, '2025-02-04 14:53:21', '2025-02-04 14:53:21');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(93, 2, 28, 'Total', 15.00, '2025-02-04 14:53:53', '2025-02-04 14:53:53');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(94, 2, 29, 'Total', 80.00, '2025-02-04 14:54:11', '2025-02-04 14:54:11');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(95, 2, 30, 'Total', 246.00, '2025-02-04 14:54:40', '2025-02-04 14:54:40');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(97, 2, 31, 'Total', 15.00, '2025-02-04 15:03:37', '2025-02-04 15:03:37');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(98, 2, 32, 'Total', 30.00, '2025-02-04 15:04:16', '2025-02-04 15:04:16');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(99, 2, 33, 'Total (% Percentage)', 10.00, '2025-02-04 15:04:41', '2025-02-04 15:04:41');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(100, 2, 34, 'Total', 10.00, '2025-02-04 15:05:06', '2025-02-04 15:05:06');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(101, 2, 35, 'Total', 6.00, '2025-02-04 15:05:33', '2025-02-04 15:05:33');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(102, 2, 36, 'Total', 9.00, '2025-02-04 15:06:03', '2025-02-04 15:06:03');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(103, 2, 37, 'Total', 5.00, '2025-02-04 15:07:43', '2025-02-04 15:07:43');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(104, 2, 38, 'Total', 1.00, '2025-02-04 15:08:04', '2025-02-04 15:08:04');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(106, 2, 39, 'Total', 7.00, '2025-02-04 15:08:57', '2025-02-04 15:08:57');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(107, 2, 40, 'Total', 6000.00, '2025-02-04 15:09:13', '2025-02-04 15:09:13');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(108, 2, 41, 'Total', 12000.00, '2025-02-04 15:09:29', '2025-02-04 15:09:29');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(109, 2, 42, 'Total', 4.00, '2025-02-04 15:10:04', '2025-02-04 15:10:04');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(110, 2, 43, 'Total (% Percentage)', 8.00, '2025-02-04 15:10:20', '2025-02-04 15:10:20');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(111, 2, 44, 'Total', 5.00, '2025-02-04 15:10:37', '2025-02-04 15:10:37');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(112, 2, 45, 'Total', 2.00, '2025-02-04 15:11:04', '2025-02-04 15:11:04');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(113, 2, 46, 'Total', 6.00, '2025-02-04 15:11:28', '2025-02-04 15:11:28');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(114, 2, 47, 'Total', 2.00, '2025-02-04 15:11:42', '2025-02-04 15:11:42');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(115, 2, 48, 'Total', 6.00, '2025-02-04 15:12:06', '2025-02-04 15:12:06');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(116, 2, 49, 'Total', 8.00, '2025-02-04 15:12:24', '2025-02-04 15:12:24');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(117, 2, 50, 'Total', 2.00, '2025-02-04 15:12:41', '2025-02-04 15:12:41');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(118, 2, 51, 'Total', 3.00, '2025-02-04 15:12:58', '2025-02-04 15:12:58');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(119, 2, 52, 'Total', 1.00, '2025-02-04 15:13:11', '2025-02-04 15:13:11');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(120, 3, 1, 'Total', 15000.00, '2025-02-04 15:23:45', '2025-02-04 15:23:45');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(121, 4, 1, 'Total', 20000.00, '2025-02-04 19:33:25', '2025-02-04 19:33:25');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(122, 4, 2, 'Total (% Percentage)', 30.00, '2025-02-04 19:33:49', '2025-02-04 19:33:49');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(123, 3, 2, 'Total', 30.00, '2025-02-04 19:34:09', '2025-02-04 19:34:09');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(124, 3, 3, 'Total (% Percentage)', 3.00, '2025-02-04 19:34:35', '2025-02-04 19:34:35');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(125, 4, 3, 'Total (% Percentage)', 3.00, '2025-02-04 19:39:23', '2025-02-04 19:39:23');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(126, 3, 4, 'Total (% Percentage)', 20.00, '2025-02-04 19:41:08', '2025-02-04 19:41:08');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(127, 4, 4, 'Total (% Percentage)', 30.00, '2025-02-04 19:42:08', '2025-02-04 19:42:08');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(128, 3, 5, 'Total (% Percentage)', 300000.00, '2025-02-04 19:42:31', '2025-02-04 19:42:31');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(129, 4, 5, 'Total (% Percentage)', 350000.00, '2025-02-04 19:42:45', '2025-02-04 19:42:45');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(130, 4, 6, 'Total (% Percentage)', 10.00, '2025-02-04 19:43:00', '2025-02-04 19:43:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(131, 3, 6, 'Total (% Percentage)', 15.00, '2025-02-04 19:43:19', '2025-02-04 19:43:19');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(132, 3, 7, 'Total (% Percentage)', 5.00, '2025-02-04 19:43:55', '2025-02-04 19:43:55');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(134, 4, 7, 'Total (% Percentage)', 5.00, '2025-02-04 19:44:29', '2025-02-04 19:44:29');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(135, 3, 8, 'Total (% Percentage)', 2.00, '2025-02-04 19:45:06', '2025-02-04 19:45:06');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(137, 4, 8, 'Total (% Percentage)', 2.00, '2025-02-04 19:45:35', '2025-02-04 19:45:35');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(138, 3, 9, 'Total', 3.00, '2025-02-04 19:47:08', '2025-02-04 19:47:08');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(139, 4, 9, 'Total', 3.00, '2025-02-04 19:47:19', '2025-02-04 19:47:19');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(140, 3, 10, 'Total', 4.00, '2025-02-04 19:47:36', '2025-02-04 19:47:36');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(141, 4, 10, 'Total', 3.00, '2025-02-04 19:47:57', '2025-02-04 19:47:57');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(142, 3, 11, 'Total', 15.00, '2025-02-04 19:48:18', '2025-02-04 19:48:18');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(143, 4, 11, 'Total', 20.00, '2025-02-04 19:48:29', '2025-02-04 19:48:29');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(144, 3, 12, 'Total', 1.00, '2025-02-04 19:48:48', '2025-02-04 19:48:48');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(145, 4, 12, 'Total', 1.00, '2025-02-04 19:48:59', '2025-02-04 19:48:59');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(146, 3, 13, 'Total', 2.00, '2025-02-04 19:50:16', '2025-02-04 19:50:16');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(147, 4, 13, 'Total', 1.00, '2025-02-04 19:50:28', '2025-02-04 19:50:28');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(148, 3, 14, 'Total', 2.00, '2025-02-04 19:50:46', '2025-02-04 19:50:46');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(149, 4, 14, 'Total', 2.00, '2025-02-04 19:50:57', '2025-02-04 19:50:57');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(150, 4, 15, 'Total', 2.00, '2025-02-04 19:51:07', '2025-02-04 19:51:07');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(151, 3, 15, 'Total', 2.00, '2025-02-04 19:51:18', '2025-02-04 19:51:18');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(152, 3, 16, 'Total', 8.00, '2025-02-04 20:12:28', '2025-02-04 20:12:28');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(153, 4, 16, 'Total', 10.00, '2025-02-04 20:12:46', '2025-02-04 20:12:46');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(154, 3, 17, 'Total', 2.00, '2025-02-04 20:13:13', '2025-02-04 20:13:13');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(155, 4, 17, 'Total', 1.00, '2025-02-04 20:13:31', '2025-02-04 20:13:31');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(156, 4, 18, 'Total', 210.00, '2025-02-04 20:14:26', '2025-02-04 20:14:26');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(157, 3, 18, 'Total', 170.00, '2025-02-04 20:14:39', '2025-02-04 20:14:39');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(163, 3, 20, 'Cassava', 10000.00, '2025-02-04 20:17:29', '2025-02-04 20:17:29');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(164, 3, 20, 'Sweet potato', 30000.00, '2025-02-04 20:17:29', '2025-02-04 20:17:29');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(165, 3, 20, 'Potato', 30.00, '2025-02-04 20:17:29', '2025-02-04 20:17:29');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(167, 4, 20, 'Cassava', 15000.00, '2025-02-04 20:18:21', '2025-02-04 20:18:21');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(168, 4, 20, 'Potato', 40.00, '2025-02-04 20:18:21', '2025-02-04 20:18:21');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(169, 4, 20, 'Sweet potato', 40000.00, '2025-02-04 20:18:21', '2025-02-04 20:18:21');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(170, 3, 21, 'Total', 6.00, '2025-02-04 20:19:24', '2025-02-04 20:19:24');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(171, 4, 21, 'Total', 6.00, '2025-02-04 20:19:34', '2025-02-04 20:19:34');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(172, 3, 22, 'Total', 0.00, '2025-02-04 20:19:51', '2025-02-04 20:19:51');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(173, 4, 22, 'Total', 0.00, '2025-02-04 20:20:03', '2025-02-04 20:20:03');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(176, 3, 24, 'Total', 9.00, '2025-02-04 20:21:46', '2025-02-04 20:21:46');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(177, 4, 24, 'Total', 11.00, '2025-02-04 20:22:06', '2025-02-04 20:22:06');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(178, 3, 25, 'Total', 200.00, '2025-02-04 20:22:25', '2025-02-04 20:22:25');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(179, 4, 25, 'Total', 250.00, '2025-02-04 20:22:37', '2025-02-04 20:22:37');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(180, 3, 26, 'Total', 10000.00, '2025-02-04 20:22:59', '2025-02-04 20:22:59');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(181, 4, 26, 'Total', 10000.00, '2025-02-04 20:23:11', '2025-02-04 20:23:11');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(182, 3, 27, 'Total', 6.00, '2025-02-04 20:24:12', '2025-02-04 20:24:12');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(183, 4, 27, 'Total', 6.00, '2025-02-04 20:24:25', '2025-02-04 20:24:25');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(184, 3, 28, 'Total', 15.00, '2025-02-04 20:24:54', '2025-02-04 20:24:54');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(185, 4, 28, 'Total', 10.00, '2025-02-04 20:25:09', '2025-02-04 20:25:09');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(186, 3, 30, 'Total', 255.00, '2025-02-04 20:25:29', '2025-02-04 20:25:29');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(187, 4, 30, 'Total', 266.00, '2025-02-04 20:25:50', '2025-02-04 20:25:50');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(188, 3, 31, 'Total', 20.00, '2025-02-04 20:26:10', '2025-02-04 20:26:10');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(189, 4, 31, 'Total', 20.00, '2025-02-04 20:26:22', '2025-02-04 20:26:22');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(190, 3, 32, 'Total', 30.00, '2025-02-04 20:26:50', '2025-02-04 20:26:50');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(191, 4, 32, 'Total', 30.00, '2025-02-04 20:27:04', '2025-02-04 20:27:04');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(194, 4, 34, 'Total', 12.00, '2025-02-04 20:28:01', '2025-02-04 20:28:01');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(195, 3, 34, 'Total', 11.00, '2025-02-04 20:28:12', '2025-02-04 20:28:12');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(196, 3, 35, 'Total', 6.00, '2025-02-04 20:28:38', '2025-02-04 20:28:38');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(197, 4, 35, 'Total', 6.00, '2025-02-04 20:28:55', '2025-02-04 20:28:55');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(198, 3, 36, 'Total', 9.00, '2025-02-04 20:29:13', '2025-02-04 20:29:13');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(199, 4, 36, 'Total', 9.00, '2025-02-04 20:29:32', '2025-02-04 20:29:32');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(200, 3, 37, 'Total', 5.00, '2025-02-04 20:29:56', '2025-02-04 20:29:56');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(201, 4, 37, 'Total', 5.00, '2025-02-04 20:30:07', '2025-02-04 20:30:07');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(202, 3, 38, 'Total', 3.00, '2025-02-04 20:30:32', '2025-02-04 20:30:32');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(203, 4, 38, 'Total', 2.00, '2025-02-04 20:30:44', '2025-02-04 20:30:44');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(204, 3, 39, 'Total', 10.00, '2025-02-04 20:31:13', '2025-02-04 20:31:13');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(205, 4, 39, 'Total', 20.00, '2025-02-04 20:31:27', '2025-02-04 20:31:27');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(206, 3, 40, 'Total', 8000.00, '2025-02-04 20:31:53', '2025-02-04 20:31:53');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(207, 4, 40, 'Total', 8000.00, '2025-02-04 20:32:11', '2025-02-04 20:32:11');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(208, 3, 41, 'Total', 18000.00, '2025-02-04 20:32:39', '2025-02-04 20:32:39');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(209, 4, 41, 'Total', 20000.00, '2025-02-04 20:32:53', '2025-02-04 20:32:53');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(210, 3, 42, 'Total', 4.00, '2025-02-04 20:33:13', '2025-02-04 20:33:13');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(211, 4, 42, 'Total', 4.00, '2025-02-04 20:33:24', '2025-02-04 20:33:24');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(212, 3, 43, 'Total', 8.00, '2025-02-04 20:33:58', '2025-02-04 20:33:58');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(213, 4, 43, 'Total', 10.00, '2025-02-04 20:34:08', '2025-02-04 20:34:08');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(214, 3, 44, 'Total', 6.00, '2025-02-04 20:34:42', '2025-02-04 20:34:42');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(215, 4, 44, 'Total', 6.00, '2025-02-04 20:34:58', '2025-02-04 20:34:58');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(216, 3, 45, 'Total', 2.00, '2025-02-04 20:35:55', '2025-02-04 20:35:55');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(217, 4, 45, 'Total', 2.00, '2025-02-04 20:36:08', '2025-02-04 20:36:08');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(218, 3, 48, 'Total', 7.00, '2025-02-04 20:36:40', '2025-02-04 20:36:40');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(219, 4, 48, 'Total', 8.00, '2025-02-04 20:36:53', '2025-02-04 20:36:53');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(220, 4, 49, 'Total', 10.00, '2025-02-04 20:38:36', '2025-02-04 20:38:36');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(221, 3, 50, 'Total', 2.00, '2025-02-04 20:39:13', '2025-02-04 20:39:13');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(222, 4, 50, 'Total', 2.00, '2025-02-04 20:39:39', '2025-02-04 20:39:39');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(223, 3, 51, 'Total', 3.00, '2025-02-04 20:39:58', '2025-02-04 20:39:58');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(224, 4, 51, 'Total', 3.00, '2025-02-04 20:40:08', '2025-02-04 20:40:08');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(225, 3, 52, 'Total', 1.00, '2025-02-04 20:40:22', '2025-02-04 20:40:22');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(226, 4, 52, 'Total', 2.00, '2025-02-04 20:40:34', '2025-02-04 20:40:34');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(227, 3, 19, 'Total (% Percentage)', 10.00, '2025-02-05 09:50:45', '2025-02-05 09:50:45');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(229, 4, 19, 'Total (% Percentage)', 10.00, '2025-02-05 09:51:06', '2025-02-05 09:51:06');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(230, 3, 23, 'Total (% Percentage)', 9.00, '2025-02-05 09:56:39', '2025-02-05 09:56:39');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(231, 4, 23, 'Total (% Percentage)', 9.00, '2025-02-05 09:56:46', '2025-02-05 09:56:46');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(232, 3, 29, 'Total', 80.00, '2025-02-05 10:06:30', '2025-02-05 10:06:30');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(233, 4, 29, 'Total', 80.00, '2025-02-05 10:06:42', '2025-02-05 10:06:42');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(234, 3, 33, 'Total (% Percentage)', 15.00, '2025-02-05 10:08:52', '2025-02-05 10:08:52');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(235, 4, 33, 'Total (% Percentage)', 25.00, '2025-02-05 10:09:00', '2025-02-05 10:09:00');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(236, 3, 46, 'Total', 6.00, '2025-02-05 10:10:58', '2025-02-05 10:10:58');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(237, 4, 46, 'Total', 6.00, '2025-02-05 10:11:08', '2025-02-05 10:11:08');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(238, 3, 47, 'Total', 2.00, '2025-02-05 10:12:30', '2025-02-05 10:12:30');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(239, 4, 47, 'Total', 2.00, '2025-02-05 10:12:42', '2025-02-05 10:12:42');
INSERT INTO cdms.submission_targets(id, financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES
(240, 3, 49, 'Total', 8.00, '2025-02-05 10:15:32', '2025-02-05 10:15:32');

        ");
    }
}
