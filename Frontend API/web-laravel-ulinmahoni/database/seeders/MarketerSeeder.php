<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Marketer;

class MarketerSeeder extends Seeder
{

    protected $data = [
        [
            'name' => 'User 01',
            'image' => 'user-28-01.jpg',
            'link' => '#0',
        ],
        [
            'name' => 'User 02',
            'image' => 'user-28-02.jpg',
            'link' => '#0',
        ],
        [
            'name' => 'User 03',
            'image' => 'user-28-03.jpg',
            'link' => '#0',
        ],
        [
            'name' => 'User 04',
            'image' => 'user-28-04.jpg',
            'link' => '#0',
        ],
        [
            'name' => 'User 05',
            'image' => 'user-28-05.jpg',
            'link' => '#0',
        ],
        [
            'name' => 'User 06',
            'image' => 'user-28-06.jpg',
            'link' => '#0',
        ],
        [
            'name' => 'User 07',
            'image' => 'user-28-07.jpg',
            'link' => '#0',
        ],
        [
            'name' => 'User 08',
            'image' => 'user-28-08.jpg',
            'link' => '#0',
        ],
        [
            'name' => 'User 09',
            'image' => 'user-28-09.jpg',
            'link' => '#0',
        ],
        [
            'name' => 'User 10',
            'image' => 'user-28-10.jpg',
            'link' => '#0',
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
            Marketer::create([
                'name' => $d['name'],
                'image' => $d['image'],
                'link' => $d['link'],
            ]);
        }
    }
}
