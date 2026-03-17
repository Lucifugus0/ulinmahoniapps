<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'm_menus'; // Specify the table name if it's not pluralized
    protected $fillable = ['header_menu', 'sub_menu', 'menu', 'kode']; // Fillable fields

    // Define the relationship with UserAccessLevel
    public function accessLevels()
    {
        return $this->hasMany(UserAccessLevel::class, 'kode_menu', 'kode');
    }
}
