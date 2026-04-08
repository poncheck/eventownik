<?php

namespace Database\Seeders;

use App\Models\EventType;
use App\Models\Menu;
use App\Models\MenuCourse;
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
