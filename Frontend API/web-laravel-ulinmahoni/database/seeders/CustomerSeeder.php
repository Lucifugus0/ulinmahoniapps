<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{

    protected $data = [
        [
            'image' => 'user-40-01.jpg',
            'name' => 'Patricia Semklo',
            'email' => 'patricia.semklo@app.com',
            'location' => '🇬🇧 London, UK',
            'orders' => '24',
            'last_order' => '#123567',
            'spent' => '$2,890.66',
            'refunds' => 0,
            'fav' => true
        ],
        [
            'image' => 'user-40-02.jpg',
            'name' => 'Dominik Lamakani',
            'email' => 'dominik.lamakani@gmail.com',
            'location' => '🇩🇪 Dortmund, DE',
            'orders' => '77',
            'last_order' => '#779912',
            'spent' => '$14,767.04',
            'refunds' => 4,
            'fav' => false
        ],
        [
            'image' => 'user-40-03.jpg',
            'name' => 'Ivan Mesaros',
            'email' => 'imivanmes@gmail.com',
            'location' => '🇫🇷 Paris, FR',
            'orders' => '44',
            'last_order' => '#889924',
            'spent' => '$4,996.00',
            'refunds' => 1,
            'fav' => true
        ],
        [
            'image' => 'user-40-04.jpg',
            'name' => 'Maria Martinez',
            'email' => 'martinezhome@gmail.com',
            'location' => '🇮🇹 Bologna, IT',
            'orders' => '29',
            'last_order' => '#897726',
            'spent' => '$3,220.66',
            'refunds' => 2,
            'fav' => false
        ],
        [
            'image' => 'user-40-05.jpg',
            'name' => 'Vicky Jung',
            'email' => 'itsvicky@contact.com',
            'location' => '🇬🇧 London, UK',
            'orders' => '22',
            'last_order' => '#123567',
            'spent' => '$2,890.66',
            'refunds' => 0,
            'fav' => true
        ],
        [
            'image' => 'user-40-06.jpg',
            'name' => 'Tisho Yanchev',
            'email' => 'tisho.y@kurlytech.com',
            'location' => '🇬🇧 London, UK',
            'orders' => '14',
            'last_order' => '#896644',
            'spent' => '$1,649.99',
            'refunds' => 1,
            'fav' => true
        ],
        [
            'image' => 'user-40-07.jpg',
            'name' => 'James Cameron',
            'email' => 'james.ceo@james.tech',
            'location' => '🇫🇷 Marseille, FR',
            'orders' => '34',
            'last_order' => '#136988',
            'spent' => '$3,569.87',
            'refunds' => 2,
            'fav' => true
        ],
        [
            'image' => 'user-40-08.jpg',
            'name' => 'Haruki Masuno',
            'email' => 'haruki@supermail.jp',
            'location' => '🇯🇵 Tokio, JP',
            'orders' => '112',
            'last_order' => '#442206',
            'spent' => '$19,246.07',
            'refunds' => 6,
            'fav' => false
        ],
        [
            'image' => 'user-40-09.jpg',
            'name' => 'Joe Huang',
            'email' => 'joehuang@hotmail.com',
            'location' => '🇨🇳 Shanghai, CN',
            'orders' => '64',
            'last_order' => '#764321',
            'spent' => '$12,276.92',
            'refunds' => 0,
            'fav' => true
        ],
        [
            'image' => 'user-40-10.jpg',
            'name' => 'Carolyn McNeail',
            'email' => 'carolynlove@gmail.com',
            'location' => '🇮🇹 Milan, IT',
            'orders' => '19',
            'last_order' => '#908764',
            'spent' => '$1,289.97',
            'refunds' => 2,
            'fav' => false
        ],
        [
            'image' => 'user-40-03.jpg',
            'name' => 'Ivan Mesaros',
            'email' => 'imivanmes@gmail.com',
            'location' => '🇫🇷 Paris, FR',
            'orders' => '44',
            'last_order' => '#889924',
            'spent' => '$4,996.00',
            'refunds' => 1,
            'fav' => true
        ],
        [
            'image' => 'user-40-04.jpg',
            'name' => 'Maria Martinez',
            'email' => 'martinezhome@gmail.com',
            'location' => '🇮🇹 Bologna, IT',
            'orders' => '29',
            'last_order' => '#897726',
            'spent' => '$3,220.66',
            'refunds' => 2,
            'fav' => false
        ],        
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach($this->data as $d) {
            Customer::create([
                'image' => $d['image'],
                'name' => $d['name'],
                'email' => $d['email'],
                'location' => $d['location'],
                'orders' => $d['orders'],
                'last_order' => $d['last_order'],
                'spent' => $d['spent'],
                'refunds' => $d['refunds'],
                'fav' => $d['fav'],
            ]);
        }
    }
}
