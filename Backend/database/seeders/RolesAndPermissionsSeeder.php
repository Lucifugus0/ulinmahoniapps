<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        
        $rolePermissions = [
            
            [1, 1],  
            [1, 2],  
            [1, 3],  
            [1, 4],  
            [1, 5],  
            [1, 6],  
            [1, 7],  
            [1, 8],  
            [1, 9],  
            [1, 10], 
            [1, 11], 
            [1, 12], 
            [1, 13], 
            [1, 14], 
            [1, 15],
            [1, 16],
            [1, 17],
            [1, 18],
            [1, 19],
            [1, 20],
            [1, 21],
            [1, 22],
            [1, 23],
            [1, 24],
            [1, 25],
            [1, 26],
            [1, 27],
            [1, 28],
            [1, 29],
            [1, 30],
            [1, 31],
            [1, 32],
            [1, 33],
            [1, 34],
            [1, 35],
            [1, 36],
            [1, 37],
            [1, 38],
            


            [2, 1],  
            [2, 2],  
            [2, 3],  
            [2, 4],  
            [2, 5],  
            [2, 6],  
            [2, 7],  
            [2, 8],  
            [2, 9],  
            [2, 10], 
            [2, 11], 
            [2, 12], 
            [2, 13], 
            [2, 14], 

            
            [3, 1],  
            [3, 2],  
            [3, 3],  
            [3, 4],  
            [3, 5],  
            [3, 7],  
            [3, 8],  
            [3, 10], 
            [3, 11], 

            
            [4, 1],  
            [4, 5],  
            [4, 11], 
            [4, 12], 
            [4, 13], 
            [4, 14], 

            
            [5, 1],  
            [5, 2],  
            [5, 5],  
            [5, 11], 
            [5, 13], 

            
            [6, 1],  
            [6, 2],  
            [6, 5],  
            [6, 9],  
            [6, 10], 
            [6, 11], 
            [6, 14], 

            
            [7, 1],  
            [7, 2],  
            [7, 3],  
            [7, 4],  
            [7, 5],  
            [7, 6],  
            [7, 7],  
            [7, 8],
            [7, 9],  
            [7, 10], 
            [7, 11], 
            [7, 12], 
            [7, 13], 
            [7, 14], 
            [7, 15], 
            [7, 16], 
            [7, 17], 
            [7, 18], 
            [7, 19], 
            [7, 20], 
            [7, 21], 
            [7, 22], 
            [7, 23], 
            [7, 24], 
            [7, 25], 
            [7, 26], 
            [7, 27], 
            [7, 28],
            [7, 29],
            [7, 32], // view_promo_banners
        ];

        $data = array_map(function ($rolePermission) use ($now) {
            return [
                'user_id' => $rolePermission[0],
                'permission_id' => $rolePermission[1],
                'created_at' => $now,
                'updated_at' => $now,
                'created_by' => 1, 
                'updated_by' => 1,
            ];
        }, $rolePermissions);

        DB::table('role_permission')->insert($data);
    }
}
