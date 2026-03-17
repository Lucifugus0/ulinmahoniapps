<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccessLevel extends Model
{
    use HasFactory;

    protected $table = 't_user_access_level'; // Specify the table name if it's not pluralized
    protected $fillable = ['iduser', 'kode_menu']; // Fillable fields

    // Define the relationship with User
    public function user()
    {
        return $this->belongsTo(User::class, 'iduser', 'id');
    }

    // Define the relationship with Menu
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'kode_menu', 'kode');
    }
}
