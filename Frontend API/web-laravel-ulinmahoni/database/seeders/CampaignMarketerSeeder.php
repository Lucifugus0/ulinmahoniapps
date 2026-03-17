<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class CampaignMarketerSeeder extends Seeder
{

    protected $data = [
        [
            'campaign_id' => 1,
            'marketer_id' => 1,
        ],
        [
            'campaign_id' => 1,
            'marketer_id' => 2,
        ],    
        [
            'campaign_id' => 1,
            'marketer_id' => 3,
        ],
        [
            'campaign_id' => 2,
            'marketer_id' => 4,
        ],    
        [
            'campaign_id' => 2,
            'marketer_id' => 5,
        ],    
        [
            'campaign_id' => 3,
            'marketer_id' => 7,
        ],    
        [
            'campaign_id' => 3,
            'marketer_id' => 8,
        ],     
        [
            'campaign_id' => 3,
            'marketer_id' => 9,
        ],
        [
            'campaign_id' => 4,
            'marketer_id' => 10,
        ],
        [
            'campaign_id' => 5,
            'marketer_id' => 11,
        ],    
        [
            'campaign_id' => 5,
            'marketer_id' => 5,
        ],     
        [
            'campaign_id' => 5,
            'marketer_id' => 12,
        ], 
        [
            'campaign_id' => 6,
            'marketer_id' => 7,
        ],    
        [
            'campaign_id' => 6,
            'marketer_id' => 4,
        ],     
        [
            'campaign_id' => 6,
            'marketer_id' => 11,
        ],
        [
            'campaign_id' => 7,
            'marketer_id' => 1,
        ],    
        [
            'campaign_id' => 7,
            'marketer_id' => 2,
        ],     
        [
            'campaign_id' => 7,
            'marketer_id' => 6,
        ],
        [
            'campaign_id' => 8,
            'marketer_id' => 9,
        ],     
        [
            'campaign_id' => 8,
            'marketer_id' => 1,
        ],  
        [
            'campaign_id' => 9,
            'marketer_id' => 7,
        ],    
        [
            'campaign_id' => 9,
            'marketer_id' => 8,
        ],     
        [
            'campaign_id' => 9,
            'marketer_id' => 9,
        ], 
        [
            'campaign_id' => 9,
            'marketer_id' => 6,
        ], 
        [
            'campaign_id' => 10,
            'marketer_id' => 6,
        ], 
        [
            'campaign_id' => 10,
            'marketer_id' => 11,
        ],
        [
            'campaign_id' => 11,
            'marketer_id' => 5,
        ],
        [
            'campaign_id' => 12,
            'marketer_id' => 7,
        ],     
        [
            'campaign_id' => 12,
            'marketer_id' => 8,
        ], 
        [
            'campaign_id' => 12,
            'marketer_id' => 9,
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
            DB::table('campaign_marketer')->insert([
                'campaign_id' => $d['campaign_id'],
                'marketer_id' => $d['marketer_id'],
            ]);
        }
    }
}
