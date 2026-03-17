<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccess extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 't_user_access_level';

    // Specify the primary key
    protected $primaryKey = 'iduser';

    // Specify the fillable properties
    protected $fillable = [
        'iduser',
        'kode_menu',
    ];

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class, 'iduser', 'id');
    }

    // Define the relationship with the Menu model
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'kode_menu', 'kode');
    }
}
