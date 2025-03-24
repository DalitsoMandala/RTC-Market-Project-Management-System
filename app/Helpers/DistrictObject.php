<?php

namespace App\Helpers;


class DistrictObject
{
    public static function districts(): array
    {
        return [
            'Balaka',
            'Blantyre',
            'Chikwawa',
            'Chiradzulu',
            'Chitipa',
            'Dedza',
            'Dowa',
            'Karonga',
            'Kasungu',
            'Lilongwe',
            'Machinga',
            'Mangochi',
            'Mchinji',
            'Mulanje',
            'Mwanza',
            'Mzimba',
            'Neno',
            'Nkhata Bay',
            'Nkhotakota',
            'Nsanje',
            'Ntcheu',
            'Ntchisi',
            'Phalombe',
            'Rumphi',
            'Salima',
            'Thyolo',
            'Zomba',
        ];
    }

    public static function approaches(): array
    {
        return [
            'Collective production only',
            'Collective marketing only',
            'Knowledge sharing only',
            'Collective production, marketing and knowledge sharing',
            'N/A'
        ];
    }

    public static function sectors(): array
    {
        return [
            'Private',
            'Public',

        ];
    }


    public static function ePAs(): array
    {
        return [
            'Kameme EPA',
            'Lufita EPA', // Chitipa District
            'Vinthukutu EPA',
            'Kaporo North EPA', // Karonga District
            'Chikwina EPA',
            'Limphasa EPA', // Nkhatabay District
            'Mphompha EPA',
            'Chiweta EPA', // Rumphi District
            'Mpherembe EPA',
            'Malidade EPA', // Mzimba District
            'Likoma EPA',
            'Chizumulu EPA', // Likoma District
            'Chamama EPA',
            'Lisasadzi EPA', // Kasungu District
            'Chipuka EPA',
            'Chikwatula EPA', // Ntchisi District
            'Mvera EPA',
            'Nachisaka EPA', // Dowa District
            'Mlonyeni EPA',
            'Chioshya EPA', // Mchinji District
            'Mwansambo EPA',
            'Linga EPA', // Nkhotakota District
            'Chiluwa EPA',
            'Chinguluwe EPA', // Salima District
            'Demera EPA',
            'Ukwe EPA', // Lilongwe District
            'Lobi EPA',
            'Chafumbwa EPA', // Dedza District
            'Nsipe EPA',
            'Manjawira EPA', // Ntcheu District
            'Nsanama EPA',
            'Nampeya EPA', // Machinga District
            'Mpilisi EPA',
            'Nansenga EPA', // Mangochi District
            'Utale EPA',
            'Phalula EPA', // Balaka District
            'Thondwe EPA',
            'Chingale EPA', // Zomba District
            'Mwanza EPA',
            'Thambani EPA', // Mwanza District
            'Neno EPA',
            'Lisungwi EPA', // Neno District
            'Lirangwe EPA',
            'Kunthembwe EPA', // Blantyre District
            'Mombezi EPA',
            'Thumbwe EPA', // Chiradzulu District
            'Matapwata EPA',
            'Thyolo Boma EPA', // Thyolo District
            'Kamwendo EPA',
            'Msikawanjala EPA', // Mulanje District
            'Naminjiwa EPA',
            'Waruma EPA', // Phalombe District
        ];
    }
    public static function categoryOrGroups(): array
    {
        return [
            'Early generation seed producer',
            'Seed multiplier',
            'Rtc producer'
        ];
    }

    public static function varieties(): array
    {
        $varieties = [
            'violet' => true,
            'rosita' => false,
            'chuma' => true,
            'mwai' => false,
            'zikomo' => true,
            'thandizo' => false,
            'royal_choice' => true,
            'kaphulira' => false,
            'chipika' => true,
            'mathuthu' => false,
            'kadyaubwelere' => true,
            'sungani' => false,
            'kajiyani' => true,
            'mugamba' => false,
            'kenya' => true,
            'nyamoyo' => false,
            'anaakwanire' => true,
            'other' => false,
        ];
        return array_keys($varieties);
    }
}
