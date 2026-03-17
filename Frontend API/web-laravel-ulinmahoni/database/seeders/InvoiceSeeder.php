<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Invoice;

class InvoiceSeeder extends Seeder
{

    protected $data = [
        [
            'invoice' => '#123567',
            'total' => '$129.00',
            'status' => 'Overdue',
            'customer' => 'Dominik Lamakani',
            'issued_date' => '2021-07-22',
            'type' => 'Subscription',
        ],
        [
            'invoice' => '#779912',
            'total' => '$59.00',
            'status' => 'Paid',
            'customer' => 'Mark Cameron',
            'issued_date' => '2021-07-19',
            'paid_date' => '2021-07-20',
            'type' => 'Subscription',
        ],
        [
            'invoice' => '#889924',
            'total' => '$89.00',
            'status' => 'Paid',
            'customer' => 'Sergio Gonnelli',
            'issued_date' => '2021-07-17',
            'paid_date' => '2021-07-19',
            'type' => 'One-time',
        ],
        [
            'invoice' => '#897726',
            'total' => '$129.00',
            'status' => 'Due',
            'customer' => 'Manuel Garbaya',
            'issued_date' => '2021-07-04',
            'type' => 'Subscription',
        ],
        [
            'invoice' => '#123567',
            'total' => '$129.00',
            'status' => 'Due',
            'customer' => 'Cool Robot',
            'issued_date' => '2021-07-04',
            'type' => 'Subscription',
        ],
        [
            'invoice' => '#896644',
            'total' => '$129.00',
            'status' => 'Paid',
            'customer' => 'Mark Cameron',
            'issued_date' => '2021-07-04',
            'paid_date' => '2021-07-09',
            'type' => 'One-time',
        ],
        [
            'invoice' => '#136988',
            'total' => '$69.00',
            'status' => 'Paid',
            'customer' => 'Glenn Thomas',
            'issued_date' => '2021-07-01',
            'paid_date' => '2021-07-01',
            'type' => 'One-time',
        ],
        [
            'invoice' => '#442206',
            'total' => '$129.00',
            'status' => 'Overdue',
            'customer' => 'Dominik Lamakani',
            'issued_date' => '2021-06-22',
            'type' => 'Subscription',
        ],
        [
            'invoice' => '#764321',
            'total' => '$89.00',
            'status' => 'Paid',
            'customer' => 'Brian Halligan',
            'issued_date' => '2021-06-21',
            'paid_date' => '2021-06-29',
            'type' => 'One-time',
        ],
        [
            'invoice' => '#908764',
            'total' => '$129.00',
            'status' => 'Due',
            'customer' => 'Carolyn McNeail',
            'issued_date' => '2021-06-17',
            'type' => 'Subscription',
        ],
        [
            'invoice' => '#897726',
            'total' => '$129.00',
            'status' => 'Due',
            'customer' => 'Manuel Garbaya',
            'issued_date' => '2021-07-04',
            'type' => 'Subscription',
        ],
        [
            'invoice' => '#123567',
            'total' => '$129.00',
            'status' => 'Due',
            'customer' => 'Cool Robot',
            'issued_date' => '2021-07-04',
            'type' => 'Subscription',
        ],
        [
            'invoice' => '#896644',
            'total' => '$129.00',
            'status' => 'Paid',
            'customer' => 'Mark Cameron',
            'issued_date' => '2021-07-04',
            'paid_date' => '2021-07-09',
            'type' => 'One-time',
        ],
        [
            'invoice' => '#136988',
            'total' => '$69.00',
            'status' => 'Paid',
            'customer' => 'Glenn Thomas',
            'issued_date' => '2021-07-01',
            'paid_date' => '2021-07-01',
            'type' => 'One-time',
        ],
        [
            'invoice' => '#442206',
            'total' => '$129.00',
            'status' => 'Overdue',
            'customer' => 'Dominik Lamakani',
            'issued_date' => '2021-06-22',
            'type' => 'Subscription',
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
            Invoice::create([
                'invoice' => $d['invoice'],
                'total' => $d['total'],
                'status' => $d['status'],
                'customer' => $d['customer'],
                'issued_date' => $d['issued_date'],
                'paid_date' => (isset($d['paid_date']) ? $d['paid_date'] : null),
                'type' => $d['type'],
            ]);
        }
    }
}
