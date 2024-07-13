<?php

namespace App\Helpers;

class Help
{


    public function __construct()
    {
        //
    }


    public static function getFakerNames()
    {
        $epaNames = [
            'Malawi Environmental Protection Agency',
            'Lilongwe Environmental Authority',
            'Blantyre Environmental Conservation Agency',
            'Mzuzu Environmental Regulatory Authority',
            'Zomba Environmental Management Board',
        ];

        $sectionNames = [
            'Environmental Impact Assessment',
            'Pollution Control and Waste Management',
            'Natural Resources Management',
            'Environmental Education and Awareness',
            'Climate Change and Resilience',
        ];

        $organisationNames = [
            'Malawi Farmers Union',
            'Lilongwe Agricultural Cooperative',
            'Blantyre Horticultural Society',
            'Mzuzu Crop Producers Association',
            'Zomba Livestock Farmers Group',
        ];

        $enterpriseNames = [
            'Malawi Agro Ventures',
            'Lilongwe Tech Innovations',
            'Blantyre Green Energy Solutions',
            'Mzuzu Organic Products',
            'Zomba Transport Services',
        ];

        $district = [
            'BALAKA',
            'BLANTYRE',
            'CHIKWAWA',
            'CHIRADZULU',
            'CHITIPA',
            'DEDZA',
            'DOWA',
            'KARONGA',
            'KASUNGU',
            'LILONGWE',
            'MACHINGA',
            'MANGOCHI',
            'MCHINJI',
            'MULANJE',
            'MWANZA',
            'MZIMBA',
            'NENO',
            'NKHATA BAY',
            'NKHOTAKOTA',
            'NSANJE',
            'NTCHEU',
            'NTCHISI',
            'PHALOMBE',
            'RUMPHI',
            'SALIMA',
            'THYOLO',
            'ZOMBA',
        ];
        $epaNames = ArrayToUpperCase::convert($epaNames);
        $organisationNames = ArrayToUpperCase::convert($organisationNames);
        $sectionNames = ArrayToUpperCase::convert($sectionNames);
        $enterpriseNames = ArrayToUpperCase::convert($enterpriseNames);
        $districts = ArrayToUpperCase::convert($district);
        return [
            'epaNames' => $epaNames,
            'organisationNames' => $organisationNames,
            'sectionNames' => $sectionNames,
            'enterpriseNames' => $enterpriseNames,
            'districtNames' => $districts,
        ];
    }
}