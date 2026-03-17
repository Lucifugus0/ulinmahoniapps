<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;

class OrderSeeder extends Seeder
{

    protected $data = [
        [
            'image' => 'icon-01.svg',
            'order' => '#123567',
            'date' => '2021-01-22',
            'customer' => 'Patricia Semklo',
            'total' => '$129.00',
            'status' => 'Refunded',
            'items' => 1,
            'location' => '🇨🇳 Shanghai, CN',
            'type' => 'Subscription',
            'description' => 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'
        ],
        [
            'image' => 'icon-01.svg',
            'order' => '#779912',
            'date' => '2021-01-22',
            'customer' => 'Dominik Lamakani',
            'total' => '$89.00',
            'status' => 'Approved',
            'items' => 2,
            'location' => '🇲🇽 Mexico City, MX',
            'type' => 'Subscription',
            'description' => 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'
        ],
        [
            'image' => 'icon-02.svg',
            'order' => '#889924',
            'date' => '2021-01-22',
            'customer' => 'Ivan Mesaros',
            'total' => '$89.00',
            'status' => 'Approved',
            'items' => 2,
            'location' => '🇮🇹 Milan, IT',
            'type' => 'One-time',
            'description' => 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'
        ],
        [
            'image' => 'icon-01.svg',
            'order' => '#897726',
            'date' => '2021-01-22',
            'customer' => 'Maria Martinez',
            'total' => '$59.00',
            'status' => 'Pending',
            'items' => 1,
            'location' => '🇮🇹 Bologna, IT',
            'type' => 'One-time',
            'description' => 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'
        ],
        [
            'image' => 'icon-03.svg',
            'order' => '#123567',
            'date' => '2021-01-22',
            'customer' => 'Vicky Jung',
            'total' => '$39.00',
            'status' => 'Refunded',
            'items' => 1,
            'location' => '🇬🇧 London, UK',
            'type' => 'Subscription',
            'description' => 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'
        ],
        [
            'image' => 'icon-01.svg',
            'order' => '#896644',
            'date' => '2021-01-21',
            'customer' => 'Tisho Yanchev',
            'total' => '$59.00',
            'status' => 'Approved',
            'items' => 1,
            'location' => '🇫🇷 Paris, FR',
            'type' => 'One-time',
            'description' => 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'
        ],
        [
            'image' => 'icon-03.svg',
            'order' => '#136988',
            'date' => '2021-01-21',
            'customer' => 'James Cameron',
            'total' => '$89.00',
            'status' => 'Approved',
            'items' => 1,
            'location' => '🇫🇷 Marseille, FR',
            'type' => 'Subscription',
            'description' => 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'
        ],
        [
            'image' => 'icon-03.svg',
            'order' => '#442206',
            'date' => '2021-01-21',
            'customer' => 'Haruki Masuno',
            'total' => '$129.00',
            'status' => 'Approved',
            'items' => 2,
            'location' => '🇺🇸 New York, USA',
            'type' => 'Subscription',
            'description' => 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'
        ],
        [
            'image' => 'icon-02.svg',
            'order' => '#764321',
            'date' => '2021-01-21',
            'customer' => 'Joe Huang',
            'total' => '$89.00',
            'status' => 'Pending',
            'items' => 2,
            'location' => '🇨🇳 Shanghai, CN',
            'type' => 'One-time',
            'description' => 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'
        ],
        [
            'image' => 'icon-01.svg',
            'order' => '#908764',
            'date' => '2021-01-21',
            'customer' => 'Carolyn McNeail',
            'total' => '$59.00',
            'status' => 'Refunded',
            'items' => 1,
            'location' => '🇬🇧 Sheffield, UK',
            'type' => 'Subscription',
            'description' => 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'
        ],
        [
            'image' => 'icon-03.svg',
            'order' => '#123567',
            'date' => '2021-01-22',
            'customer' => 'Vicky Jung',
            'total' => '$39.00',
            'status' => 'Refunded',
            'items' => 1,
            'location' => '🇬🇧 London, UK',
            'type' => 'Subscription',
            'description' => 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'
        ],
        [
            'image' => 'icon-01.svg',
            'order' => '#896644',
            'date' => '2021-01-21',
            'customer' => 'Tisho Yanchev',
            'total' => '$59.00',
            'status' => 'Approved',
            'items' => 1,
            'location' => '🇫🇷 Paris, FR',
            'type' => 'One-time',
            'description' => 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'
        ],
        [
            'image' => 'icon-02.svg',
            'order' => '#764321',
            'date' => '2021-01-21',
            'customer' => 'Joe Huang',
            'total' => '$89.00',
            'status' => 'Pending',
            'items' => 2,
            'location' => '🇨🇳 Shanghai, CN',
            'type' => 'One-time',
            'description' => 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'
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
            Order::create([
                'image' => $d['image'],
                'order' => $d['order'],
                'date' => $d['date'],
                'customer' => $d['customer'],
                'total' => $d['total'],
                'status' => $d['status'],
                'items' => $d['items'],
                'location' => $d['location'],
                'type' => $d['type'],
                'description' => $d['description'],
            ]);
        }
    }
}
