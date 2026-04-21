<?php

namespace App\Traits;

use Illuminate\Support\Facades\Route;

trait BatchTrait
{
    //

public function getBatch(){
    if($this->getRouteParams()){

        return $this->getRouteParams()['batch'] ?? null;
    }
    return null;
}

public function getCropType(){

    return $this->getRouteParams()['crop'] ?? null;
}

    private function getRouteParams()
    {

        return Route::current()->parameters();
    }
}
