<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Member;

class MemberSeeder extends Seeder
{

    protected $data = [
        [
            'name' => 'Dominik McNeail',
            'image' => 'user-64-01.jpg',
            'link' => '#0',
            'location' => '🇮🇹',
            'content' => 'Fitness Fanatic, Design Enthusiast, Mentor, Meetup Organizer & PHP Lover.',
        ],
        [
            'name' => 'Ivan Mesaros',
            'image' => 'user-64-02.jpg',
            'link' => '#0',
            'location' => '🇫🇷',
            'content' => 'Fitness Fanatic, Design Enthusiast, Mentor, Meetup Organizer & PHP Lover.',
        ],
        [
            'name' => 'Tisha Yanchev',
            'image' => 'user-64-03.jpg',
            'link' => '#0',
            'location' => '🇩🇪',
            'content' => 'Fitness Fanatic, Design Enthusiast, Mentor, Meetup Organizer & PHP Lover.',
        ],
        [
            'name' => 'Sergio Gonnelli',
            'image' => 'user-64-04.jpg',
            'link' => '#0',
            'location' => '🇮🇹',
            'content' => 'Fitness Fanatic, Design Enthusiast, Mentor, Meetup Organizer & PHP Lover.',
        ],
        [
            'name' => 'Jerzy Wierzy',
            'image' => 'user-64-05.jpg',
            'link' => '#0',
            'location' => '🇪🇸',
            'content' => 'Fitness Fanatic, Design Enthusiast, Mentor, Meetup Organizer & PHP Lover.',
        ],
        [
            'name' => 'Mirko Grubisic',
            'image' => 'user-64-06.jpg',
            'link' => '#0',
            'location' => '🇩🇪',
            'content' => 'Fitness Fanatic, Design Enthusiast, Mentor, Meetup Organizer & PHP Lover.',
        ],
        [
            'name' => 'Alisha Acharya',
            'image' => 'user-64-07.jpg',
            'link' => '#0',
            'location' => '🇬🇧',
            'content' => 'Fitness Fanatic, Design Enthusiast, Mentor, Meetup Organizer & PHP Lover.',
        ],
        [
            'name' => 'Brian Halligan',
            'image' => 'user-64-08.jpg',
            'link' => '#0',
            'location' => '🇺🇸',
            'content' => 'Fitness Fanatic, Design Enthusiast, Mentor, Meetup Organizer & PHP Lover.',
        ],
        [
            'name' => 'Patricia Semklo',
            'image' => 'user-64-09.jpg',
            'link' => '#0',
            'location' => '🇮🇳',
            'content' => 'Fitness Fanatic, Design Enthusiast, Mentor, Meetup Organizer & PHP Lover.',
        ],
        [
            'name' => 'Maria Martinez',
            'image' => 'user-64-10.jpg',
            'link' => '#0',
            'location' => '🇮🇹',
            'content' => 'Fitness Fanatic, Design Enthusiast, Mentor, Meetup Organizer & PHP Lover.',
        ],
        [
            'name' => 'Vedad Siljak',
            'image' => 'user-64-11.jpg',
            'link' => '#0',
            'location' => '🇮🇹🇨🇦',
            'content' => 'Fitness Fanatic, Design Enthusiast, Mentor, Meetup Organizer & PHP Lover.',
        ],
        [
            'name' => 'Dominik Lamakani',
            'image' => 'user-64-12.jpg',
            'link' => '#0',
            'location' => '🇧🇪',
            'content' => 'Fitness Fanatic, Design Enthusiast, Mentor, Meetup Organizer & PHP Lover.',
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
            Member::create([
                'name' => $d['name'],
                'image' => $d['image'],
                'link' => $d['link'],
                'location' => $d['location'],
                'content' => $d['content'],
            ]);
        }
    }
}
