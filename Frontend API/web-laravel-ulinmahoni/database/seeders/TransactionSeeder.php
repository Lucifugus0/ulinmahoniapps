<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Transaction;

class TransactionSeeder extends Seeder
{

    protected $data = [
        [
            'image' => 'transactions-image-01.svg',
            'name' => 'Form Builder CP',
            'status' => 'Pending',
            'amount' => '-$1,299.22',
        ],
        [
            'image' => 'transactions-image-02.svg',
            'name' => 'Imperial Hotel ****',
            'status' => 'Completed',
            'amount' => '-$1,029.77',
        ],
        [
            'image' => 'user-36-05.jpg',
            'name' => 'Aprilynne Pills',
            'status' => 'Pending',
            'amount' => '+$499.99',
        ],
        [
            'image' => 'transactions-image-03.svg',
            'name' => 'Google Limited UK',
            'status' => 'Completed',
            'amount' => '-$1,029.77',
        ],
        [
            'image' => 'transactions-image-04.svg',
            'name' => 'Acme LTD UK',
            'status' => 'Pending',
            'amount' => '+$2,179.36',
        ],
        [
            'image' => 'transactions-image-03.svg',
            'name' => 'Google Limited UK',
            'status' => 'Canceled',
            'amount' => '-$1,029.77',
        ],
        [
            'image' => 'transactions-image-05.svg',
            'name' => 'Uber',
            'status' => 'Completed',
            'amount' => '-$272.88',
        ],
        [
            'image' => 'transactions-image-06.svg',
            'name' => 'PublicOne Inc.',
            'status' => 'Completed',
            'amount' => '-$199.87',
        ],
        [
            'image' => 'transactions-image-07.svg',
            'name' => 'Github Inc.',
            'status' => 'Completed',
            'amount' => '-$42.87',
        ],
        [
            'image' => 'transactions-image-08.svg',
            'name' => 'Form Builder PRO',
            'status' => 'Completed',
            'amount' => '-$112.44',
        ],
        [
            'image' => 'transactions-image-05.svg',
            'name' => 'Google Limited UK',
            'status' => 'Canceled',
            'amount' => '-$1,029.77',
        ],
        [
            'image' => 'transactions-image-06.svg',
            'name' => 'Uber',
            'status' => 'Completed',
            'amount' => '-$272.88',
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
            Transaction::create([
                'image' => $d['image'],
                'name' => $d['name'],
                'status' => $d['status'],
                'amount' => $d['amount'],
            ]);
        }
    }
}
