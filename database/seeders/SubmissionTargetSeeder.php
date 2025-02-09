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
    public function run()
    {
        //


        //      $cleanedSql = preg_replace('/\(\d+,\s/', '(', $data);



        DB::unprepared("
      INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 3, 'Total (% Percentage)', 5.00, '2025-01-31 09:50:59', '2025-01-31 09:50:59');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 5, 'Total', 100000.00, '2025-01-31 09:50:59', '2025-01-31 09:50:59');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 6, 'Total (% Percentage)', 5.00, '2025-01-31 09:50:59', '2025-01-31 09:50:59');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 8, 'Total', 6.00, '2025-01-31 09:50:59', '2025-01-31 09:50:59');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 9, 'Total', 2.00, '2025-01-31 09:50:59', '2025-01-31 09:50:59');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 10, 'Total', 2.00, '2025-01-31 09:50:59', '2025-01-31 09:50:59');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 11, 'Total (% Percentage)', 5.00, '2025-01-31 09:50:59', '2025-01-31 09:50:59');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 12, 'Total', 1.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 13, 'Total', 3.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 14, 'Total', 3.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 15, 'Total', 1.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 16, 'Total', 5.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 17, 'Total', 2.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 18, 'Total', 107.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 19, 'Total (% Percentage)', 5.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 20, 'Cassava', 3000.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 20, 'Potato', 10.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 20, 'Sweet potato', 12000.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 21, 'Total', 6.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 22, 'Total', 1.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 23, 'Total (% Percentage)', 9.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 24, 'Total', 8.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 25, 'Total', 100.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 26, 'Total', 10000.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 27, 'Total', 6.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 28, 'Total', 12.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 29, 'Total', 50.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 30, 'Total', 180.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 31, 'Total', 10.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 32, 'Total', 30.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 33, 'Total (% Percentage)', 5.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 34, 'Total', 8.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 35, 'Total', 5.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 36, 'Total', 9.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 37, 'Total', 5.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 38, 'Total', 1.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 39, 'Total', 3.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 40, 'Total', 3000.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 41, 'Total', 8000.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 42, 'Total', 3.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 43, 'Total (% Percentage)', 5.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 44, 'Total', 3.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 45, 'Total', 2.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 46, 'Total', 6.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 47, 'Total', 2.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 48, 'Total', 6.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 49, 'Total', 6.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 50, 'Total', 6.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 51, 'Total', 3.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 52, 'Total', 1.00, '2025-01-31 09:51:00', '2025-01-31 09:51:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 2, 'Total (% Percentage)', 5.00, '2025-02-03 11:01:50', '2025-02-03 11:01:50');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 4, 'Total (% Percentage)', 0.00, '2025-02-03 11:01:50', '2025-02-03 11:01:50');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 7, 'Total (% Percentage)', 0.00, '2025-02-03 11:01:50', '2025-02-03 11:01:50');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(1, 1, 'Total', 10000.00, '2025-02-04 13:53:30', '2025-02-04 13:53:30');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 1, 'Total', 15000.00, '2025-02-04 14:01:51', '2025-02-04 14:01:51');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 2, 'Total (% Percentage)', 10.00, '2025-02-04 14:05:47', '2025-02-04 14:05:47');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 4, 'Total (% Percentage)', 5.00, '2025-02-04 14:08:49', '2025-02-04 14:08:49');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 3, 'Total (% Percentage)', 3.00, '2025-02-04 14:09:22', '2025-02-04 14:09:22');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 5, 'Total', 250000.00, '2025-02-04 14:10:09', '2025-02-04 14:10:09');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 6, 'Total (% Percentage)', 10.00, '2025-02-04 14:13:34', '2025-02-04 14:13:34');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 7, 'Total (% Percentage)', 5.00, '2025-02-04 14:14:04', '2025-02-04 14:14:04');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 8, 'Total', 6.00, '2025-02-04 14:14:34', '2025-02-04 14:14:34');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 9, 'Total', 3.00, '2025-02-04 14:15:24', '2025-02-04 14:15:24');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 10, 'Total', 3.00, '2025-02-04 14:15:41', '2025-02-04 14:15:41');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 11, 'Total (% Percentage)', 10.00, '2025-02-04 14:18:31', '2025-02-04 14:18:31');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 12, 'Total', 1.00, '2025-02-04 14:18:46', '2025-02-04 14:18:46');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 13, 'Total', 1.00, '2025-02-04 14:19:53', '2025-02-04 14:19:53');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 14, 'Total', 2.00, '2025-02-04 14:43:54', '2025-02-04 14:43:54');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 15, 'Total', 2.00, '2025-02-04 14:44:05', '2025-02-04 14:44:05');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 16, 'Total', 6.00, '2025-02-04 14:44:20', '2025-02-04 14:44:20');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 17, 'Total', 2.00, '2025-02-04 14:45:06', '2025-02-04 14:45:06');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 18, 'Total', 130.00, '2025-02-04 14:45:34', '2025-02-04 14:45:34');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 19, 'Total (% Percentage)', 10.00, '2025-02-04 14:46:11', '2025-02-04 14:46:11');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 20, 'Potato', 20.00, '2025-02-04 14:46:53', '2025-02-04 14:46:53');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 20, 'Sweet potato', 20000.00, '2025-02-04 14:46:53', '2025-02-04 14:46:53');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 20, 'Cassava', 5000.00, '2025-02-04 14:46:53', '2025-02-04 14:46:53');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 21, 'Total', 6.00, '2025-02-04 14:47:54', '2025-02-04 14:47:54');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 22, 'Total', 1.00, '2025-02-04 14:50:42', '2025-02-04 14:50:42');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 23, 'Total (% Percentage)', 9.00, '2025-02-04 14:51:53', '2025-02-04 14:51:53');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 24, 'Total', 9.00, '2025-02-04 14:52:13', '2025-02-04 14:52:13');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 25, 'Total', 150.00, '2025-02-04 14:52:30', '2025-02-04 14:52:30');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 26, 'Total', 10000.00, '2025-02-04 14:52:49', '2025-02-04 14:52:49');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 27, 'Total', 6.00, '2025-02-04 14:53:21', '2025-02-04 14:53:21');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 28, 'Total', 15.00, '2025-02-04 14:53:53', '2025-02-04 14:53:53');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 29, 'Total', 80.00, '2025-02-04 14:54:11', '2025-02-04 14:54:11');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 30, 'Total', 246.00, '2025-02-04 14:54:40', '2025-02-04 14:54:40');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 31, 'Total', 15.00, '2025-02-04 15:03:37', '2025-02-04 15:03:37');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 32, 'Total', 30.00, '2025-02-04 15:04:16', '2025-02-04 15:04:16');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 33, 'Total (% Percentage)', 10.00, '2025-02-04 15:04:41', '2025-02-04 15:04:41');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 34, 'Total', 10.00, '2025-02-04 15:05:06', '2025-02-04 15:05:06');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 35, 'Total', 6.00, '2025-02-04 15:05:33', '2025-02-04 15:05:33');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 36, 'Total', 9.00, '2025-02-04 15:06:03', '2025-02-04 15:06:03');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 37, 'Total', 5.00, '2025-02-04 15:07:43', '2025-02-04 15:07:43');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 38, 'Total', 1.00, '2025-02-04 15:08:04', '2025-02-04 15:08:04');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 39, 'Total', 7.00, '2025-02-04 15:08:57', '2025-02-04 15:08:57');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 40, 'Total', 6000.00, '2025-02-04 15:09:13', '2025-02-04 15:09:13');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 41, 'Total', 12000.00, '2025-02-04 15:09:29', '2025-02-04 15:09:29');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 42, 'Total', 4.00, '2025-02-04 15:10:04', '2025-02-04 15:10:04');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 43, 'Total (% Percentage)', 8.00, '2025-02-04 15:10:20', '2025-02-04 15:10:20');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 44, 'Total', 5.00, '2025-02-04 15:10:37', '2025-02-04 15:10:37');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 45, 'Total', 2.00, '2025-02-04 15:11:04', '2025-02-04 15:11:04');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 46, 'Total', 6.00, '2025-02-04 15:11:28', '2025-02-04 15:11:28');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 47, 'Total', 2.00, '2025-02-04 15:11:42', '2025-02-04 15:11:42');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 48, 'Total', 6.00, '2025-02-04 15:12:06', '2025-02-04 15:12:06');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 49, 'Total', 8.00, '2025-02-04 15:12:24', '2025-02-04 15:12:24');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 50, 'Total', 2.00, '2025-02-04 15:12:41', '2025-02-04 15:12:41');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 51, 'Total', 3.00, '2025-02-04 15:12:58', '2025-02-04 15:12:58');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(2, 52, 'Total', 1.00, '2025-02-04 15:13:11', '2025-02-04 15:13:11');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 1, 'Total', 15000.00, '2025-02-04 15:23:45', '2025-02-04 15:23:45');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 1, 'Total', 20000.00, '2025-02-04 19:33:25', '2025-02-04 19:33:25');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 2, 'Total (% Percentage)', 30.00, '2025-02-04 19:33:49', '2025-02-04 19:33:49');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 2, 'Total', 30.00, '2025-02-04 19:34:09', '2025-02-04 19:34:09');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 3, 'Total (% Percentage)', 3.00, '2025-02-04 19:34:35', '2025-02-04 19:34:35');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 3, 'Total (% Percentage)', 3.00, '2025-02-04 19:39:23', '2025-02-04 19:39:23');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 4, 'Total (% Percentage)', 20.00, '2025-02-04 19:41:08', '2025-02-04 19:41:08');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 4, 'Total (% Percentage)', 30.00, '2025-02-04 19:42:08', '2025-02-04 19:42:08');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 5, 'Total (% Percentage)', 300000.00, '2025-02-04 19:42:31', '2025-02-04 19:42:31');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 5, 'Total (% Percentage)', 350000.00, '2025-02-04 19:42:45', '2025-02-04 19:42:45');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 6, 'Total (% Percentage)', 10.00, '2025-02-04 19:43:00', '2025-02-04 19:43:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 6, 'Total (% Percentage)', 15.00, '2025-02-04 19:43:19', '2025-02-04 19:43:19');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 7, 'Total (% Percentage)', 5.00, '2025-02-04 19:43:55', '2025-02-04 19:43:55');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 7, 'Total (% Percentage)', 5.00, '2025-02-04 19:44:29', '2025-02-04 19:44:29');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 8, 'Total (% Percentage)', 2.00, '2025-02-04 19:45:06', '2025-02-04 19:45:06');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 8, 'Total (% Percentage)', 2.00, '2025-02-04 19:45:35', '2025-02-04 19:45:35');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 9, 'Total', 3.00, '2025-02-04 19:47:08', '2025-02-04 19:47:08');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 9, 'Total', 3.00, '2025-02-04 19:47:19', '2025-02-04 19:47:19');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 10, 'Total', 4.00, '2025-02-04 19:47:36', '2025-02-04 19:47:36');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 10, 'Total', 3.00, '2025-02-04 19:47:57', '2025-02-04 19:47:57');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 11, 'Total', 15.00, '2025-02-04 19:48:18', '2025-02-04 19:48:18');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 11, 'Total', 20.00, '2025-02-04 19:48:29', '2025-02-04 19:48:29');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 12, 'Total', 1.00, '2025-02-04 19:48:48', '2025-02-04 19:48:48');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 12, 'Total', 1.00, '2025-02-04 19:48:59', '2025-02-04 19:48:59');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 13, 'Total', 2.00, '2025-02-04 19:50:16', '2025-02-04 19:50:16');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 13, 'Total', 1.00, '2025-02-04 19:50:28', '2025-02-04 19:50:28');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 14, 'Total', 2.00, '2025-02-04 19:50:46', '2025-02-04 19:50:46');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 14, 'Total', 2.00, '2025-02-04 19:50:57', '2025-02-04 19:50:57');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 15, 'Total', 2.00, '2025-02-04 19:51:07', '2025-02-04 19:51:07');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 15, 'Total', 2.00, '2025-02-04 19:51:18', '2025-02-04 19:51:18');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 16, 'Total', 8.00, '2025-02-04 20:12:28', '2025-02-04 20:12:28');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 16, 'Total', 10.00, '2025-02-04 20:12:46', '2025-02-04 20:12:46');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 17, 'Total', 2.00, '2025-02-04 20:13:13', '2025-02-04 20:13:13');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 17, 'Total', 1.00, '2025-02-04 20:13:31', '2025-02-04 20:13:31');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 18, 'Total', 210.00, '2025-02-04 20:14:26', '2025-02-04 20:14:26');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 18, 'Total', 170.00, '2025-02-04 20:14:39', '2025-02-04 20:14:39');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 20, 'Cassava', 10000.00, '2025-02-04 20:17:29', '2025-02-04 20:17:29');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 20, 'Sweet potato', 30000.00, '2025-02-04 20:17:29', '2025-02-04 20:17:29');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 20, 'Potato', 30.00, '2025-02-04 20:17:29', '2025-02-04 20:17:29');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 20, 'Cassava', 15000.00, '2025-02-04 20:18:21', '2025-02-04 20:18:21');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 20, 'Potato', 40.00, '2025-02-04 20:18:21', '2025-02-04 20:18:21');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 20, 'Sweet potato', 40000.00, '2025-02-04 20:18:21', '2025-02-04 20:18:21');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 21, 'Total', 6.00, '2025-02-04 20:19:24', '2025-02-04 20:19:24');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 21, 'Total', 6.00, '2025-02-04 20:19:34', '2025-02-04 20:19:34');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 22, 'Total', 0.00, '2025-02-04 20:19:51', '2025-02-04 20:19:51');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 22, 'Total', 0.00, '2025-02-04 20:20:03', '2025-02-04 20:20:03');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 24, 'Total', 9.00, '2025-02-04 20:21:46', '2025-02-04 20:21:46');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 24, 'Total', 11.00, '2025-02-04 20:22:06', '2025-02-04 20:22:06');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 25, 'Total', 200.00, '2025-02-04 20:22:25', '2025-02-04 20:22:25');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 25, 'Total', 250.00, '2025-02-04 20:22:37', '2025-02-04 20:22:37');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 26, 'Total', 10000.00, '2025-02-04 20:22:59', '2025-02-04 20:22:59');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 26, 'Total', 10000.00, '2025-02-04 20:23:11', '2025-02-04 20:23:11');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 27, 'Total', 6.00, '2025-02-04 20:24:12', '2025-02-04 20:24:12');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 27, 'Total', 6.00, '2025-02-04 20:24:25', '2025-02-04 20:24:25');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 28, 'Total', 15.00, '2025-02-04 20:24:54', '2025-02-04 20:24:54');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 28, 'Total', 10.00, '2025-02-04 20:25:09', '2025-02-04 20:25:09');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 30, 'Total', 255.00, '2025-02-04 20:25:29', '2025-02-04 20:25:29');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 30, 'Total', 266.00, '2025-02-04 20:25:50', '2025-02-04 20:25:50');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 31, 'Total', 20.00, '2025-02-04 20:26:10', '2025-02-04 20:26:10');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 31, 'Total', 20.00, '2025-02-04 20:26:22', '2025-02-04 20:26:22');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 32, 'Total', 30.00, '2025-02-04 20:26:50', '2025-02-04 20:26:50');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 32, 'Total', 30.00, '2025-02-04 20:27:04', '2025-02-04 20:27:04');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 34, 'Total', 12.00, '2025-02-04 20:28:01', '2025-02-04 20:28:01');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 34, 'Total', 11.00, '2025-02-04 20:28:12', '2025-02-04 20:28:12');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 35, 'Total', 6.00, '2025-02-04 20:28:38', '2025-02-04 20:28:38');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 35, 'Total', 6.00, '2025-02-04 20:28:55', '2025-02-04 20:28:55');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 36, 'Total', 9.00, '2025-02-04 20:29:13', '2025-02-04 20:29:13');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 36, 'Total', 9.00, '2025-02-04 20:29:32', '2025-02-04 20:29:32');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 37, 'Total', 5.00, '2025-02-04 20:29:56', '2025-02-04 20:29:56');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 37, 'Total', 5.00, '2025-02-04 20:30:07', '2025-02-04 20:30:07');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 38, 'Total', 3.00, '2025-02-04 20:30:32', '2025-02-04 20:30:32');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 38, 'Total', 2.00, '2025-02-04 20:30:44', '2025-02-04 20:30:44');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 39, 'Total', 10.00, '2025-02-04 20:31:13', '2025-02-04 20:31:13');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 39, 'Total', 20.00, '2025-02-04 20:31:27', '2025-02-04 20:31:27');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 40, 'Total', 8000.00, '2025-02-04 20:31:53', '2025-02-04 20:31:53');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 40, 'Total', 8000.00, '2025-02-04 20:32:11', '2025-02-04 20:32:11');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 41, 'Total', 18000.00, '2025-02-04 20:32:39', '2025-02-04 20:32:39');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 41, 'Total', 20000.00, '2025-02-04 20:32:53', '2025-02-04 20:32:53');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 42, 'Total', 4.00, '2025-02-04 20:33:13', '2025-02-04 20:33:13');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 42, 'Total', 4.00, '2025-02-04 20:33:24', '2025-02-04 20:33:24');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 43, 'Total', 8.00, '2025-02-04 20:33:58', '2025-02-04 20:33:58');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 43, 'Total', 10.00, '2025-02-04 20:34:08', '2025-02-04 20:34:08');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 44, 'Total', 6.00, '2025-02-04 20:34:42', '2025-02-04 20:34:42');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 44, 'Total', 6.00, '2025-02-04 20:34:58', '2025-02-04 20:34:58');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 45, 'Total', 2.00, '2025-02-04 20:35:55', '2025-02-04 20:35:55');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 45, 'Total', 2.00, '2025-02-04 20:36:08', '2025-02-04 20:36:08');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 48, 'Total', 7.00, '2025-02-04 20:36:40', '2025-02-04 20:36:40');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 48, 'Total', 8.00, '2025-02-04 20:36:53', '2025-02-04 20:36:53');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 49, 'Total', 10.00, '2025-02-04 20:38:36', '2025-02-04 20:38:36');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 50, 'Total', 2.00, '2025-02-04 20:39:13', '2025-02-04 20:39:13');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 50, 'Total', 2.00, '2025-02-04 20:39:39', '2025-02-04 20:39:39');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 51, 'Total', 3.00, '2025-02-04 20:39:58', '2025-02-04 20:39:58');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 51, 'Total', 3.00, '2025-02-04 20:40:08', '2025-02-04 20:40:08');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 52, 'Total', 1.00, '2025-02-04 20:40:22', '2025-02-04 20:40:22');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 52, 'Total', 2.00, '2025-02-04 20:40:34', '2025-02-04 20:40:34');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 19, 'Total (% Percentage)', 10.00, '2025-02-05 09:50:45', '2025-02-05 09:50:45');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 19, 'Total (% Percentage)', 10.00, '2025-02-05 09:51:06', '2025-02-05 09:51:06');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 23, 'Total (% Percentage)', 9.00, '2025-02-05 09:56:39', '2025-02-05 09:56:39');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 23, 'Total (% Percentage)', 9.00, '2025-02-05 09:56:46', '2025-02-05 09:56:46');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 29, 'Total', 80.00, '2025-02-05 10:06:30', '2025-02-05 10:06:30');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 29, 'Total', 80.00, '2025-02-05 10:06:42', '2025-02-05 10:06:42');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 33, 'Total (% Percentage)', 15.00, '2025-02-05 10:08:52', '2025-02-05 10:08:52');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 33, 'Total (% Percentage)', 25.00, '2025-02-05 10:09:00', '2025-02-05 10:09:00');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 46, 'Total', 6.00, '2025-02-05 10:10:58', '2025-02-05 10:10:58');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 46, 'Total', 6.00, '2025-02-05 10:11:08', '2025-02-05 10:11:08');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 47, 'Total', 2.00, '2025-02-05 10:12:30', '2025-02-05 10:12:30');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(4, 47, 'Total', 2.00, '2025-02-05 10:12:42', '2025-02-05 10:12:42');


INSERT INTO cdms.submission_targets(financial_year_id, indicator_id, target_name, target_value, created_at, updated_at) VALUES


(3, 49, 'Total', 8.00, '2025-02-05 10:15:32', '2025-02-05 10:15:32');
");
    }
}