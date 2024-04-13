<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    use HasFactory;

    protected $table = "artists";
    protected $fillable = ['f_name', 'l_name', 'gender', 'country', 'image'];


    //----------------------------
    public function songs()
    {
        return $this->hasMany(Song::class);
    }
}
