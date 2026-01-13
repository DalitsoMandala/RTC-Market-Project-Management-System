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
            'Other (Int.)'
        ];
    }

    public static function countries(): array
    {
        return  [
            'Afghanistan',
            'Albania',
            'Algeria',
            'Andorra',
            'Angola',
            'Antigua and Barbuda',
            'Argentina',
            'Armenia',
            'Australia',
            'Austria',
            'Azerbaijan',
            'Bahamas',
            'Bahrain',
            'Bangladesh',
            'Barbados',
            'Belarus',
            'Belgium',
            'Belize',
            'Benin',
            'Bhutan',
            'Bolivia',
            'Bosnia and Herzegovina',
            'Botswana',
            'Brazil',
            'Brunei',
            'Bulgaria',
            'Burkina Faso',
            'Burundi',
            'Cabo Verde',
            'Cambodia',
            'Cameroon',
            'Canada',
            'Central African Republic',
            'Chad',
            'Chile',
            'China',
            'Colombia',
            'Comoros',
            'Congo Republic',
            'Congo Democratic Republic',
            'Costa Rica',
            'Croatia',
            'Cuba',
            'Cyprus',
            'Czech Republic',
            'Denmark',
            'Djibouti',
            'Dominica',
            'Dominican Republic',
            'Ecuador',
            'Egypt',
            'El Salvador',
            'Equatorial Guinea',
            'Eritrea',
            'Estonia',
            'Eswatini',
            'Ethiopia',
            'Fiji',
            'Finland',
            'France',
            'Gabon',
            'Gambia',
            'Georgia',
            'Germany',
            'Ghana',
            'Greece',
            'Grenada',
            'Guatemala',
            'Guinea',
            'Guinea-Bissau',
            'Guyana',
            'Haiti',
            'Honduras',
            'Hungary',
            'Iceland',
            'India',
            'Indonesia',
            'Iran',
            'Iraq',
            'Ireland',
            'Israel',
            'Italy',
            'Jamaica',
            'Japan',
            'Jordan',
            'Kazakhstan',
            'Kenya',
            'Kiribati',
            'Korea North',
            'Korea South',
            'Kuwait',
            'Kyrgyzstan',
            'Laos',
            'Latvia',
            'Lebanon',
            'Lesotho',
            'Liberia',
            'Libya',
            'Liechtenstein',
            'Lithuania',
            'Luxembourg',
            'Madagascar',
            'Malawi',
            'Malaysia',
            'Maldives',
            'Mali',
            'Malta',
            'Marshall Islands',
            'Mauritania',
            'Mauritius',
            'Mexico',
            'Micronesia',
            'Moldova',
            'Monaco',
            'Mongolia',
            'Montenegro',
            'Morocco',
            'Mozambique',
            'Myanmar',
            'Namibia',
            'Nauru',
            'Nepal',
            'Netherlands',
            'New Zealand',
            'Nicaragua',
            'Niger',
            'Nigeria',
            'North Macedonia',
            'Norway',
            'Oman',
            'Pakistan',
            'Palau',
            'Panama',
            'Papua New Guinea',
            'Paraguay',
            'Peru',
            'Philippines',
            'Poland',
            'Portugal',
            'Qatar',
            'Romania',
            'Russia',
            'Rwanda',
            'Saint Kitts and Nevis',
            'Saint Lucia',
            'Saint Vincent and the Grenadines',
            'Samoa',
            'San Marino',
            'Sao Tome and Principe',
            'Saudi Arabia',
            'Senegal',
            'Serbia',
            'Seychelles',
            'Sierra Leone',
            'Singapore',
            'Slovakia',
            'Slovenia',
            'Solomon Islands',
            'Somalia',
            'South Africa',
            'South Sudan',
            'Spain',
            'Sri Lanka',
            'Sudan',
            'Suriname',
            'Sweden',
            'Switzerland',
            'Syria',
            'Taiwan',
            'Tajikistan',
            'Tanzania',
            'Thailand',
            'Timor-Leste',
            'Togo',
            'Tonga',
            'Trinidad and Tobago',
            'Tunisia',
            'Turkey',
            'Turkmenistan',
            'Tuvalu',
            'Uganda',
            'Ukraine',
            'United Arab Emirates',
            'United Kingdom',
            'United States',
            'Uruguay',
            'Uzbekistan',
            'Vanuatu',
            'Vatican City',
            'Venezuela',
            'Vietnam',
            'Yemen',
            'Zambia',
            'Zimbabwe',
            'Ivory Coast'
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
