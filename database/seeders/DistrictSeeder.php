<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\City;
use App\Models\District;
use Illuminate\Database\Seeder;

/**
 * Seeds Berlin's 12 official Bezirke by default. Idempotent — keyed on
 * (city_id, permalink) so re-running only updates existing rows.
 *
 * Universal by design — while the shipped catalogue is Berlin, the
 * seeder is parameterised. Other city catalogues (München's 25
 * Stadtbezirke, Hamburg's 7 Bezirke, …) can be added over time by
 * dropping into `CATALOG` below and running the seeder with the target
 * city permalink.
 *
 * Usage:
 *   php artisan db:seed --class=DistrictSeeder
 *
 * The postal code prefix is a hint (Berlin's districts each span
 * multiple prefixes) — admins can refine per district after seeding.
 */
final class DistrictSeeder extends Seeder
{
    /**
     * Per-city district catalogues. Add new cities as separate keys.
     *
     * @var array<string, list<array{name: string, code: string, permalink: string, postal_code_prefix: string, is_popular?: bool}>>
     */
    private const CATALOG = [
        // Berlin — 12 official Bezirke.
        'berlin' => [
            ['name' => 'Mitte', 'code' => 'MI', 'permalink' => 'mitte', 'postal_code_prefix' => '10115', 'is_popular' => true],
            ['name' => 'Friedrichshain-Kreuzberg', 'code' => 'FK', 'permalink' => 'friedrichshain-kreuzberg', 'postal_code_prefix' => '10243', 'is_popular' => true],
            ['name' => 'Pankow', 'code' => 'PK', 'permalink' => 'pankow', 'postal_code_prefix' => '13187'],
            ['name' => 'Charlottenburg-Wilmersdorf', 'code' => 'CW', 'permalink' => 'charlottenburg-wilmersdorf', 'postal_code_prefix' => '10585', 'is_popular' => true],
            ['name' => 'Spandau', 'code' => 'SP', 'permalink' => 'spandau', 'postal_code_prefix' => '13581'],
            ['name' => 'Steglitz-Zehlendorf', 'code' => 'SZ', 'permalink' => 'steglitz-zehlendorf', 'postal_code_prefix' => '12163'],
            ['name' => 'Tempelhof-Schöneberg', 'code' => 'TS', 'permalink' => 'tempelhof-schoeneberg', 'postal_code_prefix' => '10777'],
            ['name' => 'Neukölln', 'code' => 'NK', 'permalink' => 'neukoelln', 'postal_code_prefix' => '12043', 'is_popular' => true],
            ['name' => 'Treptow-Köpenick', 'code' => 'TK', 'permalink' => 'treptow-koepenick', 'postal_code_prefix' => '12435'],
            ['name' => 'Marzahn-Hellersdorf', 'code' => 'MH', 'permalink' => 'marzahn-hellersdorf', 'postal_code_prefix' => '12679'],
            ['name' => 'Lichtenberg', 'code' => 'LI', 'permalink' => 'lichtenberg', 'postal_code_prefix' => '10315'],
            ['name' => 'Reinickendorf', 'code' => 'RI', 'permalink' => 'reinickendorf', 'postal_code_prefix' => '13407'],
        ],

        // Hamburg — 7 official Bezirke.
        'hamburg' => [
            ['name' => 'Hamburg-Mitte', 'code' => 'HM', 'permalink' => 'hamburg-mitte', 'postal_code_prefix' => '20095', 'is_popular' => true],
            ['name' => 'Altona', 'code' => 'AL', 'permalink' => 'altona', 'postal_code_prefix' => '22765', 'is_popular' => true],
            ['name' => 'Eimsbüttel', 'code' => 'EI', 'permalink' => 'eimsbuettel', 'postal_code_prefix' => '20259'],
            ['name' => 'Hamburg-Nord', 'code' => 'HN', 'permalink' => 'hamburg-nord', 'postal_code_prefix' => '22303', 'is_popular' => true],
            ['name' => 'Wandsbek', 'code' => 'WA', 'permalink' => 'wandsbek', 'postal_code_prefix' => '22041'],
            ['name' => 'Bergedorf', 'code' => 'BE', 'permalink' => 'bergedorf', 'postal_code_prefix' => '21029'],
            ['name' => 'Harburg', 'code' => 'HA', 'permalink' => 'harburg', 'postal_code_prefix' => '21073'],
        ],

        // München — 12 popular Stadtbezirke (of 25 total).
        'muenchen' => [
            ['name' => 'Altstadt-Lehel', 'code' => 'ALT', 'permalink' => 'altstadt-lehel', 'postal_code_prefix' => '80331', 'is_popular' => true],
            ['name' => 'Ludwigsvorstadt-Isarvorstadt', 'code' => 'LIV', 'permalink' => 'ludwigsvorstadt-isarvorstadt', 'postal_code_prefix' => '80336'],
            ['name' => 'Maxvorstadt', 'code' => 'MAX', 'permalink' => 'maxvorstadt', 'postal_code_prefix' => '80333', 'is_popular' => true],
            ['name' => 'Schwabing-West', 'code' => 'SWW', 'permalink' => 'schwabing-west', 'postal_code_prefix' => '80798', 'is_popular' => true],
            ['name' => 'Au-Haidhausen', 'code' => 'AHH', 'permalink' => 'au-haidhausen', 'postal_code_prefix' => '81667'],
            ['name' => 'Sendling', 'code' => 'SEN', 'permalink' => 'sendling', 'postal_code_prefix' => '81371'],
            ['name' => 'Schwanthalerhöhe', 'code' => 'SWH', 'permalink' => 'schwanthalerhoehe', 'postal_code_prefix' => '80339'],
            ['name' => 'Neuhausen-Nymphenburg', 'code' => 'NHN', 'permalink' => 'neuhausen-nymphenburg', 'postal_code_prefix' => '80637'],
            ['name' => 'Schwabing-Freimann', 'code' => 'SWF', 'permalink' => 'schwabing-freimann', 'postal_code_prefix' => '80805'],
            ['name' => 'Bogenhausen', 'code' => 'BOG', 'permalink' => 'bogenhausen', 'postal_code_prefix' => '81675', 'is_popular' => true],
            ['name' => 'Pasing-Obermenzing', 'code' => 'POB', 'permalink' => 'pasing-obermenzing', 'postal_code_prefix' => '81241'],
            ['name' => 'Ramersdorf-Perlach', 'code' => 'RPE', 'permalink' => 'ramersdorf-perlach', 'postal_code_prefix' => '81737'],
        ],

        // Köln — 9 Stadtbezirke.
        'koeln' => [
            ['name' => 'Innenstadt', 'code' => 'IN', 'permalink' => 'innenstadt', 'postal_code_prefix' => '50667', 'is_popular' => true],
            ['name' => 'Rodenkirchen', 'code' => 'RO', 'permalink' => 'rodenkirchen', 'postal_code_prefix' => '50996'],
            ['name' => 'Lindenthal', 'code' => 'LI', 'permalink' => 'lindenthal', 'postal_code_prefix' => '50931', 'is_popular' => true],
            ['name' => 'Ehrenfeld', 'code' => 'EH', 'permalink' => 'ehrenfeld', 'postal_code_prefix' => '50823', 'is_popular' => true],
            ['name' => 'Nippes', 'code' => 'NI', 'permalink' => 'nippes', 'postal_code_prefix' => '50733'],
            ['name' => 'Chorweiler', 'code' => 'CH', 'permalink' => 'chorweiler', 'postal_code_prefix' => '50765'],
            ['name' => 'Porz', 'code' => 'PO', 'permalink' => 'porz', 'postal_code_prefix' => '51143'],
            ['name' => 'Kalk', 'code' => 'KA', 'permalink' => 'kalk', 'postal_code_prefix' => '51103'],
            ['name' => 'Mülheim', 'code' => 'MU', 'permalink' => 'muelheim', 'postal_code_prefix' => '51063'],
        ],

        // Frankfurt am Main — 10 popular Ortsbezirke (of 16 total).
        'frankfurt-am-main' => [
            ['name' => 'Innenstadt I', 'code' => 'I1', 'permalink' => 'innenstadt-i', 'postal_code_prefix' => '60311', 'is_popular' => true],
            ['name' => 'Innenstadt II', 'code' => 'I2', 'permalink' => 'innenstadt-ii', 'postal_code_prefix' => '60316'],
            ['name' => 'Innenstadt III', 'code' => 'I3', 'permalink' => 'innenstadt-iii', 'postal_code_prefix' => '60322'],
            ['name' => 'Bornheim/Ostend', 'code' => 'BO', 'permalink' => 'bornheim-ostend', 'postal_code_prefix' => '60385', 'is_popular' => true],
            ['name' => 'Süd', 'code' => 'SU', 'permalink' => 'sued', 'postal_code_prefix' => '60594', 'is_popular' => true],
            ['name' => 'West', 'code' => 'WE', 'permalink' => 'west', 'postal_code_prefix' => '60486'],
            ['name' => 'Mitte-Nord', 'code' => 'MN', 'permalink' => 'mitte-nord', 'postal_code_prefix' => '60435'],
            ['name' => 'Nord-Ost', 'code' => 'NO', 'permalink' => 'nord-ost', 'postal_code_prefix' => '60433'],
            ['name' => 'Nord-West', 'code' => 'NW', 'permalink' => 'nord-west', 'postal_code_prefix' => '60439'],
            ['name' => 'Ost', 'code' => 'OS', 'permalink' => 'ost', 'postal_code_prefix' => '60388'],
        ],

        // Düsseldorf — 10 Stadtbezirke.
        'duesseldorf' => [
            ['name' => 'Altstadt/Carlstadt', 'code' => 'D01', 'permalink' => 'altstadt-carlstadt', 'postal_code_prefix' => '40213', 'is_popular' => true],
            ['name' => 'Flingern/Düsseltal', 'code' => 'D02', 'permalink' => 'flingern-duesseltal', 'postal_code_prefix' => '40233'],
            ['name' => 'Oberbilk/Friedrichstadt', 'code' => 'D03', 'permalink' => 'oberbilk-friedrichstadt', 'postal_code_prefix' => '40215'],
            ['name' => 'Oberkassel/Heerdt', 'code' => 'D04', 'permalink' => 'oberkassel-heerdt', 'postal_code_prefix' => '40545', 'is_popular' => true],
            ['name' => 'Kaiserswerth/Wittlaer', 'code' => 'D05', 'permalink' => 'kaiserswerth-wittlaer', 'postal_code_prefix' => '40489'],
            ['name' => 'Rath/Unterrath', 'code' => 'D06', 'permalink' => 'rath-unterrath', 'postal_code_prefix' => '40472'],
            ['name' => 'Gerresheim/Grafenberg', 'code' => 'D07', 'permalink' => 'gerresheim-grafenberg', 'postal_code_prefix' => '40625'],
            ['name' => 'Eller/Lierenfeld', 'code' => 'D08', 'permalink' => 'eller-lierenfeld', 'postal_code_prefix' => '40229'],
            ['name' => 'Wersten/Holthausen', 'code' => 'D09', 'permalink' => 'wersten-holthausen', 'postal_code_prefix' => '40591'],
            ['name' => 'Benrath/Garath', 'code' => 'D10', 'permalink' => 'benrath-garath', 'postal_code_prefix' => '40597'],
        ],

        // Stuttgart — 5 innere Stadtbezirke (main urban ones).
        'stuttgart' => [
            ['name' => 'Stuttgart-Mitte', 'code' => 'SM', 'permalink' => 'stuttgart-mitte', 'postal_code_prefix' => '70173', 'is_popular' => true],
            ['name' => 'Stuttgart-Nord', 'code' => 'SN', 'permalink' => 'stuttgart-nord', 'postal_code_prefix' => '70191'],
            ['name' => 'Stuttgart-Ost', 'code' => 'SO', 'permalink' => 'stuttgart-ost', 'postal_code_prefix' => '70188', 'is_popular' => true],
            ['name' => 'Stuttgart-Süd', 'code' => 'SS', 'permalink' => 'stuttgart-sued', 'postal_code_prefix' => '70178'],
            ['name' => 'Stuttgart-West', 'code' => 'SW', 'permalink' => 'stuttgart-west', 'postal_code_prefix' => '70176', 'is_popular' => true],
        ],

        // Bremen — 5 Stadtbezirke.
        'bremen' => [
            ['name' => 'Mitte', 'code' => 'BM', 'permalink' => 'bremen-mitte', 'postal_code_prefix' => '28195', 'is_popular' => true],
            ['name' => 'Nord', 'code' => 'BN', 'permalink' => 'bremen-nord', 'postal_code_prefix' => '28755'],
            ['name' => 'Ost', 'code' => 'BO', 'permalink' => 'bremen-ost', 'postal_code_prefix' => '28329'],
            ['name' => 'Süd', 'code' => 'BS', 'permalink' => 'bremen-sued', 'postal_code_prefix' => '28197'],
            ['name' => 'West', 'code' => 'BW', 'permalink' => 'bremen-west', 'postal_code_prefix' => '28237'],
        ],

        // Dresden — 5 popular Stadtbezirke (of 10 total).
        'dresden' => [
            ['name' => 'Altstadt', 'code' => 'ALT', 'permalink' => 'dresden-altstadt', 'postal_code_prefix' => '01067', 'is_popular' => true],
            ['name' => 'Neustadt', 'code' => 'NEU', 'permalink' => 'dresden-neustadt', 'postal_code_prefix' => '01097', 'is_popular' => true],
            ['name' => 'Pieschen', 'code' => 'PIE', 'permalink' => 'pieschen', 'postal_code_prefix' => '01127'],
            ['name' => 'Cotta', 'code' => 'COT', 'permalink' => 'cotta', 'postal_code_prefix' => '01157'],
            ['name' => 'Blasewitz', 'code' => 'BLA', 'permalink' => 'blasewitz', 'postal_code_prefix' => '01277'],
        ],

        // Leipzig — 5 popular Stadtbezirke (of 10 total).
        'leipzig' => [
            ['name' => 'Mitte', 'code' => 'LMI', 'permalink' => 'leipzig-mitte', 'postal_code_prefix' => '04109', 'is_popular' => true],
            ['name' => 'Nord', 'code' => 'LNO', 'permalink' => 'leipzig-nord', 'postal_code_prefix' => '04129'],
            ['name' => 'Ost', 'code' => 'LOS', 'permalink' => 'leipzig-ost', 'postal_code_prefix' => '04315'],
            ['name' => 'Süd', 'code' => 'LSU', 'permalink' => 'leipzig-sued', 'postal_code_prefix' => '04275', 'is_popular' => true],
            ['name' => 'West', 'code' => 'LWE', 'permalink' => 'leipzig-west', 'postal_code_prefix' => '04229'],
        ],

        // Hannover — 6 popular Stadtbezirke.
        'hannover' => [
            ['name' => 'Mitte', 'code' => 'HAM', 'permalink' => 'hannover-mitte', 'postal_code_prefix' => '30159', 'is_popular' => true],
            ['name' => 'Vahrenwald-List', 'code' => 'HAV', 'permalink' => 'vahrenwald-list', 'postal_code_prefix' => '30161'],
            ['name' => 'Bothfeld-Vahrenheide', 'code' => 'HAB', 'permalink' => 'bothfeld-vahrenheide', 'postal_code_prefix' => '30659'],
            ['name' => 'Buchholz-Kleefeld', 'code' => 'HABK', 'permalink' => 'buchholz-kleefeld', 'postal_code_prefix' => '30627'],
            ['name' => 'Linden-Limmer', 'code' => 'HAL', 'permalink' => 'linden-limmer', 'postal_code_prefix' => '30451', 'is_popular' => true],
            ['name' => 'Südstadt-Bult', 'code' => 'HAS', 'permalink' => 'suedstadt-bult', 'postal_code_prefix' => '30171'],
        ],

        // Nürnberg — 5 known Statistische Bezirke.
        'nuernberg' => [
            ['name' => 'Altstadt', 'code' => 'NALT', 'permalink' => 'nuernberg-altstadt', 'postal_code_prefix' => '90403', 'is_popular' => true],
            ['name' => 'Gostenhof', 'code' => 'NGO', 'permalink' => 'gostenhof', 'postal_code_prefix' => '90429', 'is_popular' => true],
            ['name' => 'St. Johannis', 'code' => 'NJO', 'permalink' => 'st-johannis', 'postal_code_prefix' => '90419'],
            ['name' => 'Wöhrd', 'code' => 'NWO', 'permalink' => 'woehrd', 'postal_code_prefix' => '90489'],
            ['name' => 'Langwasser', 'code' => 'NLA', 'permalink' => 'langwasser', 'postal_code_prefix' => '90471'],
        ],

        // Essen — 5 known Stadtbezirke.
        'essen' => [
            ['name' => 'Stadtkern', 'code' => 'ESK', 'permalink' => 'essen-stadtkern', 'postal_code_prefix' => '45127', 'is_popular' => true],
            ['name' => 'Rüttenscheid', 'code' => 'ERU', 'permalink' => 'ruettenscheid', 'postal_code_prefix' => '45130', 'is_popular' => true],
            ['name' => 'Werden', 'code' => 'EWE', 'permalink' => 'werden', 'postal_code_prefix' => '45239'],
            ['name' => 'Kettwig', 'code' => 'EKE', 'permalink' => 'kettwig', 'postal_code_prefix' => '45219'],
            ['name' => 'Steele', 'code' => 'EST', 'permalink' => 'steele', 'postal_code_prefix' => '45276'],
        ],

        // Dortmund — 5 known Stadtbezirke.
        'dortmund' => [
            ['name' => 'Innenstadt-West', 'code' => 'DIW', 'permalink' => 'dortmund-innenstadt-west', 'postal_code_prefix' => '44137', 'is_popular' => true],
            ['name' => 'Innenstadt-Nord', 'code' => 'DIN', 'permalink' => 'dortmund-innenstadt-nord', 'postal_code_prefix' => '44145'],
            ['name' => 'Innenstadt-Ost', 'code' => 'DIO', 'permalink' => 'dortmund-innenstadt-ost', 'postal_code_prefix' => '44135', 'is_popular' => true],
            ['name' => 'Hörde', 'code' => 'DHO', 'permalink' => 'hoerde', 'postal_code_prefix' => '44263'],
            ['name' => 'Aplerbeck', 'code' => 'DAP', 'permalink' => 'aplerbeck', 'postal_code_prefix' => '44287'],
        ],

        // Bonn — 4 Stadtbezirke.
        'bonn' => [
            ['name' => 'Bonn', 'code' => 'BOB', 'permalink' => 'bonn-innenstadt', 'postal_code_prefix' => '53111', 'is_popular' => true],
            ['name' => 'Bad Godesberg', 'code' => 'BOG', 'permalink' => 'bad-godesberg', 'postal_code_prefix' => '53177'],
            ['name' => 'Beuel', 'code' => 'BEU', 'permalink' => 'beuel', 'postal_code_prefix' => '53225'],
            ['name' => 'Hardtberg', 'code' => 'HAR', 'permalink' => 'hardtberg', 'postal_code_prefix' => '53123'],
        ],

        // Freiburg — 4 known Stadtbezirke.
        'freiburg' => [
            ['name' => 'Altstadt', 'code' => 'FAL', 'permalink' => 'freiburg-altstadt', 'postal_code_prefix' => '79098', 'is_popular' => true],
            ['name' => 'Wiehre', 'code' => 'FWI', 'permalink' => 'wiehre', 'postal_code_prefix' => '79102', 'is_popular' => true],
            ['name' => 'Vauban', 'code' => 'FVA', 'permalink' => 'vauban', 'postal_code_prefix' => '79100'],
            ['name' => 'Rieselfeld', 'code' => 'FRI', 'permalink' => 'rieselfeld', 'postal_code_prefix' => '79111'],
        ],

        // Karlsruhe — 4 known Stadtteile.
        'karlsruhe' => [
            ['name' => 'Innenstadt-West', 'code' => 'KIW', 'permalink' => 'karlsruhe-innenstadt-west', 'postal_code_prefix' => '76133', 'is_popular' => true],
            ['name' => 'Innenstadt-Ost', 'code' => 'KIO', 'permalink' => 'karlsruhe-innenstadt-ost', 'postal_code_prefix' => '76131'],
            ['name' => 'Südstadt', 'code' => 'KSU', 'permalink' => 'karlsruhe-suedstadt', 'postal_code_prefix' => '76137'],
            ['name' => 'Weststadt', 'code' => 'KWE', 'permalink' => 'karlsruhe-weststadt', 'postal_code_prefix' => '76135'],
        ],

        // Mannheim — 4 known Stadtbezirke.
        'mannheim' => [
            ['name' => 'Innenstadt/Jungbusch', 'code' => 'MAJ', 'permalink' => 'mannheim-innenstadt-jungbusch', 'postal_code_prefix' => '68159', 'is_popular' => true],
            ['name' => 'Neckarstadt', 'code' => 'MAN', 'permalink' => 'neckarstadt', 'postal_code_prefix' => '68167'],
            ['name' => 'Lindenhof', 'code' => 'MAL', 'permalink' => 'lindenhof-mannheim', 'postal_code_prefix' => '68163'],
            ['name' => 'Neuostheim/Neuhermsheim', 'code' => 'MANH', 'permalink' => 'neuostheim-neuhermsheim', 'postal_code_prefix' => '68163'],
        ],

        // Augsburg — 4 known Stadtbezirke.
        'augsburg' => [
            ['name' => 'Innenstadt', 'code' => 'AIN', 'permalink' => 'augsburg-innenstadt', 'postal_code_prefix' => '86150', 'is_popular' => true],
            ['name' => 'Lechhausen', 'code' => 'ALE', 'permalink' => 'lechhausen', 'postal_code_prefix' => '86165'],
            ['name' => 'Pfersee', 'code' => 'APF', 'permalink' => 'pfersee', 'postal_code_prefix' => '86157'],
            ['name' => 'Kriegshaber', 'code' => 'AKR', 'permalink' => 'kriegshaber', 'postal_code_prefix' => '86156'],
        ],

        // Wiesbaden — 4 known Ortsbezirke.
        'wiesbaden' => [
            ['name' => 'Mitte', 'code' => 'WMI', 'permalink' => 'wiesbaden-mitte', 'postal_code_prefix' => '65183', 'is_popular' => true],
            ['name' => 'Nordost', 'code' => 'WNO', 'permalink' => 'wiesbaden-nordost', 'postal_code_prefix' => '65193'],
            ['name' => 'Rheingauviertel/Hollerborn', 'code' => 'WRH', 'permalink' => 'rheingauviertel-hollerborn', 'postal_code_prefix' => '65197'],
            ['name' => 'Biebrich', 'code' => 'WBI', 'permalink' => 'biebrich', 'postal_code_prefix' => '65203'],
        ],

        // Bochum — 4 known Stadtbezirke.
        'bochum' => [
            ['name' => 'Mitte', 'code' => 'BOMI', 'permalink' => 'bochum-mitte', 'postal_code_prefix' => '44787', 'is_popular' => true],
            ['name' => 'Nord', 'code' => 'BOMN', 'permalink' => 'bochum-nord', 'postal_code_prefix' => '44809'],
            ['name' => 'Wattenscheid', 'code' => 'BOWA', 'permalink' => 'wattenscheid', 'postal_code_prefix' => '44866'],
            ['name' => 'Süd', 'code' => 'BOSU', 'permalink' => 'bochum-sued', 'postal_code_prefix' => '44797'],
        ],

        // Braunschweig — 4 known Stadtbezirke.
        'braunschweig' => [
            ['name' => 'Innenstadt', 'code' => 'BSI', 'permalink' => 'braunschweig-innenstadt', 'postal_code_prefix' => '38100', 'is_popular' => true],
            ['name' => 'Westliches Ringgebiet', 'code' => 'BSW', 'permalink' => 'westliches-ringgebiet', 'postal_code_prefix' => '38118'],
            ['name' => 'Östliches Ringgebiet', 'code' => 'BSO', 'permalink' => 'oestliches-ringgebiet', 'postal_code_prefix' => '38102'],
            ['name' => 'Nördliches Ringgebiet', 'code' => 'BSN', 'permalink' => 'noerdliches-ringgebiet', 'postal_code_prefix' => '38106'],
        ],

        // Kiel — 4 known Stadtteile.
        'kiel' => [
            ['name' => 'Altstadt', 'code' => 'KIA', 'permalink' => 'kiel-altstadt', 'postal_code_prefix' => '24103', 'is_popular' => true],
            ['name' => 'Vorstadt', 'code' => 'KIV', 'permalink' => 'kiel-vorstadt', 'postal_code_prefix' => '24103'],
            ['name' => 'Ravensberg', 'code' => 'KIR', 'permalink' => 'ravensberg', 'postal_code_prefix' => '24118'],
            ['name' => 'Gaarden', 'code' => 'KIG', 'permalink' => 'gaarden', 'postal_code_prefix' => '24143'],
        ],

        // Lübeck — 4 known Stadtteile.
        'luebeck' => [
            ['name' => 'Innenstadt', 'code' => 'LUI', 'permalink' => 'luebeck-innenstadt', 'postal_code_prefix' => '23552', 'is_popular' => true],
            ['name' => 'St. Jürgen', 'code' => 'LUJ', 'permalink' => 'st-juergen', 'postal_code_prefix' => '23564'],
            ['name' => 'St. Lorenz Süd', 'code' => 'LULS', 'permalink' => 'st-lorenz-sued', 'postal_code_prefix' => '23558'],
            ['name' => 'St. Lorenz Nord', 'code' => 'LULN', 'permalink' => 'st-lorenz-nord', 'postal_code_prefix' => '23554'],
        ],

        // Mainz — 4 known Stadtbezirke.
        'mainz' => [
            ['name' => 'Altstadt', 'code' => 'MZA', 'permalink' => 'mainz-altstadt', 'postal_code_prefix' => '55116', 'is_popular' => true],
            ['name' => 'Neustadt', 'code' => 'MZN', 'permalink' => 'mainz-neustadt', 'postal_code_prefix' => '55118'],
            ['name' => 'Oberstadt', 'code' => 'MZO', 'permalink' => 'mainz-oberstadt', 'postal_code_prefix' => '55131'],
            ['name' => 'Gonsenheim', 'code' => 'MZG', 'permalink' => 'gonsenheim', 'postal_code_prefix' => '55122'],
        ],
    ];

    public function run(): void
    {
        $skipped = [];
        $seededCount = 0;
        $cityCount = 0;

        foreach (self::CATALOG as $cityPermalink => $districts) {
            /**
             * A single city name may appear more than once in `cities`
             * (CSV import round-trips with slightly-different permalinks:
             * `koeln`/`koln`, `luebeck`/`lubeck`, …). We seed districts
             * for ALL matching city_ids so both variants are populated.
             */
            $cities = City::query()
                ->where(function ($q) use ($cityPermalink): void {
                    $q->where('permalink', $cityPermalink);
                    $variant = self::spellingVariant($cityPermalink);
                    if ($variant !== null) {
                        $q->orWhere('permalink', $variant);
                    }
                })
                ->get();

            if ($cities->isEmpty()) {
                $skipped[] = $cityPermalink;

                continue;
            }

            foreach ($cities as $city) {
                foreach ($districts as $index => $data) {
                    District::query()->updateOrCreate(
                        ['city_id' => $city->id, 'permalink' => $data['permalink']],
                        [
                            'name' => $data['name'],
                            'code' => $data['code'],
                            'postal_code_prefix' => $data['postal_code_prefix'],
                            'is_popular' => $data['is_popular'] ?? false,
                            'status' => 'published',
                            'sort_order' => $index + 1,
                        ],
                    );
                }
                $seededCount += count($districts);
                $cityCount++;
            }
        }

        $this->command?->info("Seeded {$seededCount} districts across {$cityCount} cities.");
        if ($skipped !== []) {
            $this->command?->warn('Skipped (city not in DB): '.implode(', ', $skipped));
        }
    }

    /**
     * Umlaut/ASCII spelling variants that Excel round-trips have
     * historically produced. Returned as a single alternative permalink
     * to also match; extend as new pairs surface.
     */
    private static function spellingVariant(string $permalink): ?string
    {
        return match ($permalink) {
            'muenchen' => 'munchendf',
            'nuernberg' => 'nurnberg',
            'koeln' => 'koln',
            'duesseldorf' => 'dusseldorf',
            'luebeck' => 'lubeck',
            'bremen' => 'bremen-stadt',
            'mannheim' => 'mannheims',
            default => null,
        };
    }
}
