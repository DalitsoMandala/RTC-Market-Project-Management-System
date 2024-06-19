<?php

namespace App\Helpers;


class ImportValidateHeading{



    public static function validateHeadings(array $headings, $expectedHeadings)
    {
        // Collect missing headings
        $missingHeadings = [];
        foreach ($expectedHeadings as $expectedHeading) {
            if (!in_array($expectedHeading, $headings)) {
                $missingHeadings[] = $expectedHeading;
            }
        }
        return $missingHeadings;
    }
}
