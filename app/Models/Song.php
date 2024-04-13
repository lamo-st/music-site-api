<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;

    protected $table = "songs";
    protected $fillable = ['title', 'type', 'price', 'artist_id'];


    //----------------------------
    public function artist()
    {
        return $this->belongsTo(Artist::class, 'artist_id');
    }

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'orders', 'song_id', 'invoice_id');
    }
}
