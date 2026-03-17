<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Job;

class JobSeeder extends Seeder
{

    protected $data = [
        [
            'image' => 'company-icon-05.svg',
            'company' => 'Company 01',
            'role' => 'Senior Web App Designer',
            'details' => 'Contract / Remote / New York, NYC',
            'type' => 'Featured',
            'fav' => false,
        ],
        [
            'image' => 'company-icon-05.svg',
            'company' => 'Company 02',
            'role' => 'Senior Full Stack Engineer',
            'details' => 'Contract / Remote / New York, NYC',
            'type' => 'New',
            'fav' => true,
        ],
        [
            'image' => 'company-icon-06.svg',
            'company' => 'Company 03',
            'role' => 'Ruby on Rails Engineer',
            'details' => 'Contract / Remote / New York, NYC',
            'type' => 'New',
            'fav' => false,
        ],
        [
            'image' => 'company-icon-03.svg',
            'company' => 'Company 04',
            'role' => 'Senior Software Engineer Backend',
            'details' => 'Full-time / Remote / Anywhere',
            'type' => 'New',
            'fav' => false,
        ],
        [
            'image' => 'company-icon-07.svg',
            'company' => 'Company 05',
            'role' => 'React.js Software Developer',
            'details' => 'Full-time / Remote / London, UK',
            'type' => 'New',
            'fav' => true,
        ],
        [
            'image' => 'company-icon-08.svg',
            'company' => 'Company 06',
            'role' => 'Senior Full Stack Rails Developer',
            'details' => 'Part-time / Remote / Milan, IT',
            'type' => 'New',
            'fav' => false,
        ],
        [
            'image' => 'company-icon-01.svg',
            'company' => 'Company 07',
            'role' => 'Principal Software Engineer',
            'details' => 'Freelance / Remote / London, UK',
            'type' => 'New',
            'fav' => false,
        ],
        [
            'image' => 'company-icon-07.svg',
            'company' => 'Company 08',
            'role' => 'Contract React Native Engineer',
            'details' => 'Contract / Remote / Miami, FL',
            'type' => 'New',
            'fav' => false,
        ],
        [
            'image' => 'company-icon-08.svg',
            'company' => 'Company 09',
            'role' => 'Senior Client Engineer (React & React Native)',
            'details' => 'Full-time / Remote / Lincoln, NE',
            'type' => 'New',
            'fav' => false,
        ],
        [
            'image' => 'company-icon-02.svg',
            'company' => 'Company 10',
            'role' => 'QA Automation Engineer',
            'details' => 'Contract / Remote / Anywhere',
            'type' => 'New',
            'fav' => false,
        ],
        [
            'image' => 'company-icon-08.svg',
            'company' => 'Company 06',
            'role' => 'Senior Full Stack Rails Developer',
            'details' => 'Part-time / Remote / Milan, IT',
            'type' => 'New',
            'fav' => false,
        ],
        [
            'image' => 'company-icon-01.svg',
            'company' => 'Company 07',
            'role' => 'Principal Software Engineer',
            'details' => 'Freelance / Remote / London, UK',
            'type' => 'New',
            'fav' => false,
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
            Job::create([
                'image' => $d['image'],
                'company' => $d['company'],
                'role' => $d['role'],
                'details' => $d['details'],
                'type' => $d['type'],
                'fav' => $d['fav'],
            ]);
        }
    }
}
