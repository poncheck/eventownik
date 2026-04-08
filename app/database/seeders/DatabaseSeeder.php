<?php

namespace Database\Seeders;

use App\Models\EventType;
use App\Models\Menu;
use App\Models\MenuCourse;
use App\Models\MenuProduct;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@eventownik.pl')],
            [
                'name'              => 'Administrator',
                'password'          => Hash::make(env('ADMIN_PASSWORD', 'changeme123!')),
                'email_verified_at' => now(),
            ]
        );

        // Typy imprez
        $eventTypes = [
            ['name' => 'Stypa',                  'slug' => 'stypa'],
            ['name' => 'Obiad okolicznościowy',   'slug' => 'obiad-okolicznosciowy'],
            ['name' => 'Chrzciny',                'slug' => 'chrzciny'],
            ['name' => 'Komunia',                 'slug' => 'komunia'],
            ['name' => 'Wesele',                  'slug' => 'wesele'],
            ['name' => 'Obiad weselny',           'slug' => 'obiad-weselny'],
            ['name' => 'Szkolenie',               'slug' => 'szkolenie'],
            ['name' => 'Konferencja',             'slug' => 'konferencja'],
        ];

        foreach ($eventTypes as $i => $data) {
            EventType::firstOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, ['active' => true, 'sort_order' => $i])
            );
        }

        // Przykładowe menu
        $menuTemplates = [
            'wesele' => [
                [
                    'name'             => 'Menu Klasyczne',
                    'description'      => 'Tradycyjne menu weselne oparte na polskiej kuchni.',
                    'price_per_person' => 180,
                    'courses' => [
                        ['type' => 'starter', 'name' => 'Roladki z łososia i serka śmietankowego'],
                        ['type' => 'soup',    'name' => 'Żurek staropolski z kiełbasą i jajkiem'],
                        ['type' => 'main',    'name' => 'Polędwiczka wieprzowa w sosie grzybowym'],
                        ['type' => 'dessert', 'name' => 'Tort weselny + kawa i herbata'],
                    ],
                ],
                [
                    'name'             => 'Menu Premium',
                    'description'      => 'Wykwintne menu z elementami kuchni europejskiej.',
                    'price_per_person' => 240,
                    'courses' => [
                        ['type' => 'starter', 'name' => 'Carpaccio z wołowiny z rukolą i parmezanem'],
                        ['type' => 'soup',    'name' => 'Krem z białych szparagów z grzankami truflowymi'],
                        ['type' => 'main',    'name' => 'Filet z dorsza lub pierś z kaczki z demi-glace'],
                        ['type' => 'dessert', 'name' => 'Tort weselny + deser bufetowy'],
                    ],
                ],
                [
                    'name'             => 'Menu Rustykal',
                    'description'      => 'Domowe, sycące menu z tradycyjnymi smakami.',
                    'price_per_person' => 150,
                    'courses' => [
                        ['type' => 'starter', 'name' => 'Deska wędlin i serów z kiszonkami'],
                        ['type' => 'soup',    'name' => 'Barszcz czerwony z uszkami'],
                        ['type' => 'main',    'name' => 'Golonka pieczona z kapustą i kopytkami'],
                        ['type' => 'dessert', 'name' => 'Szarlotka z lodami i bitą śmietaną'],
                    ],
                ],
            ],
            'komunia' => [
                [
                    'name'             => 'Menu Komunijne Podstawowe',
                    'description'      => 'Lekkie menu odpowiednie dla całej rodziny.',
                    'price_per_person' => 120,
                    'courses' => [
                        ['type' => 'starter', 'name' => 'Sałatka grecka'],
                        ['type' => 'soup',    'name' => 'Rosół z kury z makaronem'],
                        ['type' => 'main',    'name' => 'Kurczak pieczony z warzywami i frytkami'],
                        ['type' => 'dessert', 'name' => 'Tort komunijny + owoce sezonowe'],
                    ],
                ],
                [
                    'name'             => 'Menu Komunijne Rozszerzone',
                    'description'      => 'Bogatsze menu z wyborem dań.',
                    'price_per_person' => 160,
                    'courses' => [
                        ['type' => 'starter', 'name' => 'Półmisek przystawek (3 rodzaje)'],
                        ['type' => 'soup',    'name' => 'Krem pomidorowy z grzankami'],
                        ['type' => 'main',    'name' => 'Schab pieczony lub filet z kurczaka — do wyboru'],
                        ['type' => 'dessert', 'name' => 'Tort + lody + ciasta domowe'],
                    ],
                ],
            ],
            'konferencja' => [
                [
                    'name'             => 'Menu Biznesowe',
                    'description'      => 'Profesjonalny catering konferencyjny.',
                    'price_per_person' => 80,
                    'courses' => [
                        ['type' => 'starter', 'name' => 'Finger food (3 rodzaje) przy rejestracji'],
                        ['type' => 'main',    'name' => 'Lunch bufetowy: zupa + 2 dania + sałatki'],
                        ['type' => 'other',   'name' => 'Przerwy kawowe: kawa, herbata, ciastka, owoce'],
                    ],
                ],
                [
                    'name'             => 'Menu Konferencyjne Light',
                    'description'      => 'Lekka obsługa cateringowa przez cały dzień.',
                    'price_per_person' => 55,
                    'courses' => [
                        ['type' => 'other', 'name' => 'Przerwy kawowe ×2: kawa, herbata, woda, ciastka'],
                        ['type' => 'main',  'name' => 'Lunch: kanapki premium, sałatki, zupy krem'],
                    ],
                ],
            ],
        ];

        foreach ($menuTemplates as $slug => $menus) {
            $eventType = EventType::where('slug', $slug)->first();
            if (! $eventType) {
                continue;
            }

            foreach ($menus as $i => $menuData) {
                $menu = Menu::firstOrCreate(
                    ['event_type_id' => $eventType->id, 'name' => $menuData['name']],
                    [
                        'description'      => $menuData['description'],
                        'price_per_person' => $menuData['price_per_person'],
                        'active'           => true,
                        'sort_order'       => $i,
                    ]
                );

                if ($menu->courses()->count() === 0) {
                    foreach ($menuData['courses'] as $j => $course) {
                        MenuCourse::create([
                            'menu_id'    => $menu->id,
                            'type'       => $course['type'],
                            'name'       => $course['name'],
                            'sort_order' => $j,
                        ]);
                    }
                }
            }
        }

        // Katalog produktów menu
        $products = [
            // Zupy
            ['category' => 'soup', 'serving_type' => null, 'name' => 'Żurek staropolski z kiełbasą i jajkiem',           'price_per_person' => 12.00, 'sort_order' => 1],
            ['category' => 'soup', 'serving_type' => null, 'name' => 'Rosół z kury z makaronem',                          'price_per_person' => 10.00, 'sort_order' => 2],
            ['category' => 'soup', 'serving_type' => null, 'name' => 'Barszcz czerwony z uszkami',                        'price_per_person' => 11.00, 'sort_order' => 3],
            ['category' => 'soup', 'serving_type' => null, 'name' => 'Krem z białych szparagów z grzankami',              'price_per_person' => 15.00, 'sort_order' => 4],
            ['category' => 'soup', 'serving_type' => null, 'name' => 'Krem pomidorowy z grzankami',                       'price_per_person' => 10.00, 'sort_order' => 5],
            ['category' => 'soup', 'serving_type' => null, 'name' => 'Zupa grzybowa z łazankami',                         'price_per_person' => 13.00, 'sort_order' => 6],

            // Przystawki
            ['category' => 'starter', 'serving_type' => null, 'name' => 'Roladki z łososia i serka śmietankowego',       'price_per_person' => 18.00, 'sort_order' => 1],
            ['category' => 'starter', 'serving_type' => null, 'name' => 'Carpaccio z wołowiny z rukolą i parmezanem',    'price_per_person' => 22.00, 'sort_order' => 2],
            ['category' => 'starter', 'serving_type' => null, 'name' => 'Deska wędlin i serów z kiszonkami',             'price_per_person' => 20.00, 'sort_order' => 3],
            ['category' => 'starter', 'serving_type' => null, 'name' => 'Sałatka grecka',                                 'price_per_person' => 14.00, 'sort_order' => 4],
            ['category' => 'starter', 'serving_type' => null, 'name' => 'Bruschetta z pomidorami i bazylią',              'price_per_person' => 12.00, 'sort_order' => 5],
            ['category' => 'starter', 'serving_type' => null, 'name' => 'Tatar z łososia na blini',                       'price_per_person' => 25.00, 'sort_order' => 6],

            // Dania główne – talerz (1 szt./os., stała porcja)
            ['category' => 'main', 'serving_type' => 'plate', 'name' => 'Polędwiczka wieprzowa w sosie grzybowym',        'price_per_person' => 45.00, 'sort_order' => 1],
            ['category' => 'main', 'serving_type' => 'plate', 'name' => 'Filet z dorsza na puree z groszku',              'price_per_person' => 40.00, 'sort_order' => 2],
            ['category' => 'main', 'serving_type' => 'plate', 'name' => 'Pierś z kaczki z demi-glace',                    'price_per_person' => 55.00, 'sort_order' => 3],
            ['category' => 'main', 'serving_type' => 'plate', 'name' => 'Kurczak pieczony z warzywami',                   'price_per_person' => 35.00, 'sort_order' => 4],
            ['category' => 'main', 'serving_type' => 'plate', 'name' => 'Schab pieczony z sosem własnym',                 'price_per_person' => 38.00, 'sort_order' => 5],
            ['category' => 'main', 'serving_type' => 'plate', 'name' => 'Łosoś pieczony z cytryną i koperkiem',           'price_per_person' => 48.00, 'sort_order' => 6],

            // Dania główne – półmisek (min. 60%)
            ['category' => 'main', 'serving_type' => 'platter', 'name' => 'Golonka pieczona',                             'price_per_person' => 38.00, 'sort_order' => 7],
            ['category' => 'main', 'serving_type' => 'platter', 'name' => 'Żeberka wędzone BBQ',                          'price_per_person' => 42.00, 'sort_order' => 8],
            ['category' => 'main', 'serving_type' => 'platter', 'name' => 'Pieczeń wieprzowa z kością',                   'price_per_person' => 40.00, 'sort_order' => 9],
            ['category' => 'main', 'serving_type' => 'platter', 'name' => 'Kurczak z rożna – cały',                       'price_per_person' => 32.00, 'sort_order' => 10],

            // Dodatki skrobiowe
            ['category' => 'side_starchy', 'serving_type' => null, 'name' => 'Ziemniaki gotowane z koperkiem',            'price_per_person' => 6.00,  'sort_order' => 1],
            ['category' => 'side_starchy', 'serving_type' => null, 'name' => 'Frytki',                                    'price_per_person' => 7.00,  'sort_order' => 2],
            ['category' => 'side_starchy', 'serving_type' => null, 'name' => 'Kopytka',                                   'price_per_person' => 8.00,  'sort_order' => 3],
            ['category' => 'side_starchy', 'serving_type' => null, 'name' => 'Ryż na sypko',                              'price_per_person' => 5.00,  'sort_order' => 4],
            ['category' => 'side_starchy', 'serving_type' => null, 'name' => 'Puree ziemniaczane',                        'price_per_person' => 7.00,  'sort_order' => 5],
            ['category' => 'side_starchy', 'serving_type' => null, 'name' => 'Kasza gryczana',                            'price_per_person' => 6.00,  'sort_order' => 6],

            // Surówki / sałatki (min. 50%)
            ['category' => 'salad', 'serving_type' => null, 'name' => 'Surówka z białej kapusty z marchewką',             'price_per_person' => 5.00,  'sort_order' => 1],
            ['category' => 'salad', 'serving_type' => null, 'name' => 'Mizeria (ogórek w śmietanie z koperkiem)',          'price_per_person' => 5.00,  'sort_order' => 2],
            ['category' => 'salad', 'serving_type' => null, 'name' => 'Surówka z czerwonej kapusty',                      'price_per_person' => 5.00,  'sort_order' => 3],
            ['category' => 'salad', 'serving_type' => null, 'name' => 'Sałatka grecka (ogórek, pomidor, feta)',            'price_per_person' => 8.00,  'sort_order' => 4],
            ['category' => 'salad', 'serving_type' => null, 'name' => 'Sałata lodowa z pomidorami i dressingiem',          'price_per_person' => 7.00,  'sort_order' => 5],
            ['category' => 'salad', 'serving_type' => null, 'name' => 'Sałatka jarzynowa',                                'price_per_person' => 6.00,  'sort_order' => 6],

            // Sosy (min. 50%)
            ['category' => 'sauce', 'serving_type' => null, 'name' => 'Sos pieczeniowy',                                  'price_per_person' => 4.00,  'sort_order' => 1],
            ['category' => 'sauce', 'serving_type' => null, 'name' => 'Sos grzybowy',                                     'price_per_person' => 6.00,  'sort_order' => 2],
            ['category' => 'sauce', 'serving_type' => null, 'name' => 'Sos śmietanowy z koperkiem',                       'price_per_person' => 5.00,  'sort_order' => 3],
            ['category' => 'sauce', 'serving_type' => null, 'name' => 'Sos czosnkowy',                                    'price_per_person' => 4.00,  'sort_order' => 4],
            ['category' => 'sauce', 'serving_type' => null, 'name' => 'Sos BBQ',                                           'price_per_person' => 4.00,  'sort_order' => 5],
            ['category' => 'sauce', 'serving_type' => null, 'name' => 'Sos pomidorowy prowansalski',                       'price_per_person' => 5.00,  'sort_order' => 6],
        ];

        foreach ($products as $data) {
            MenuProduct::firstOrCreate(
                ['name' => $data['name'], 'category' => $data['category']],
                array_merge($data, ['active' => true])
            );
        }

        // Przykładowa sala
        Room::firstOrCreate(
            ['name' => 'Sala Główna'],
            [
                'description'    => 'Przestronna sala na parterze, pojemność do 150 osób.',
                'capacity_min'   => 20,
                'capacity_max'   => 150,
                'price_per_hour' => 200,
                'active'         => true,
            ]
        );
    }
}
