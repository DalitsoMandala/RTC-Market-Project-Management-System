<?php

namespace App\Helpers;

class CoreFunctions
{
    public static function getCropsWithNull()
    {
        return [
            'Cassava',
            'Potato',
            'Sweet potato',
            null
        ];
    }
}
