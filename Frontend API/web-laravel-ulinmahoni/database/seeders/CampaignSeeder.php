<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Campaign;

class CampaignSeeder extends Seeder
{

    protected $data = [
        [
            'category' => '1',
            'title' => 'Monitor progress in Real Time Value',
            'content' => 'Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts.',
            'start_date' => '2022-01-20',
            'end_date' => '2022-01-27',
            'type' => 'One-Time'
        ],
        [
            'category' => '2',
            'title' => 'Monitor progress in Real Time Value',
            'content' => 'Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts.',
            'start_date' => '2022-01-20',
            'end_date' => '2022-01-27',
            'type' => 'Off-Track'
        ],
        [
            'category' => '3',
            'title' => 'Monitor progress in Real Time Value',
            'content' => 'Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts.',
            'start_date' => '2022-01-20',
            'end_date' => '2022-01-27',
            'type' => 'At Risk'
        ],
        [
            'category' => '1',
            'title' => 'Monitor progress in Real Time Value',
            'content' => 'Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts.',
            'start_date' => '2022-01-20',
            'end_date' => '2022-01-27',
            'type' => 'At Risk'
        ],
        [
            'category' => '4',
            'title' => 'Monitor progress in Real Time Value',
            'content' => 'Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts.',
            'start_date' => '2022-01-20',
            'end_date' => '2022-01-27',
            'type' => 'One-Time'
        ],
        [
            'category' => '2',
            'title' => 'Monitor progress in Real Time Value',
            'content' => 'Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts.',
            'start_date' => '2022-01-20',
            'end_date' => '2022-01-27',
            'type' => 'At Risk'
        ],
        [
            'category' => '4',
            'title' => 'Monitor progress in Real Time Value',
            'content' => 'Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts.',
            'start_date' => '2022-01-20',
            'end_date' => '2022-01-27',
            'type' => 'One-Time'
        ],
        [
            'category' => '1',
            'title' => 'Monitor progress in Real Time Value',
            'content' => 'Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts.',
            'start_date' => '2022-01-20',
            'end_date' => '2022-01-27',
            'type' => 'Off-Track'
        ],
        [
            'category' => '3',
            'title' => 'Monitor progress in Real Time Value',
            'content' => 'Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts.',
            'start_date' => '2022-01-20',
            'end_date' => '2022-01-27',
            'type' => 'One-Time'
        ],
        [
            'category' => '4',
            'title' => 'Monitor progress in Real Time Value',
            'content' => 'Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts.',
            'start_date' => '2022-01-20',
            'end_date' => '2022-01-27',
            'type' => 'Off-Track'
        ],
        [
            'category' => '2',
            'title' => 'Monitor progress in Real Time Value',
            'content' => 'Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts.',
            'start_date' => '2022-01-20',
            'end_date' => '2022-01-27',
            'type' => 'Off-Track'
        ],
        [
            'category' => '3',
            'title' => 'Monitor progress in Real Time Value',
            'content' => 'Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts.',
            'start_date' => '2022-01-20',
            'end_date' => '2022-01-27',
            'type' => 'At Risk'
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
            Campaign::create([
                'category' => $d['category'],
                'title' => $d['title'],
                'content' => $d['content'],
                'start_date' => $d['start_date'],
                'end_date' => $d['end_date'],
                'type' => $d['type'],
            ]);
        }
    }
}
