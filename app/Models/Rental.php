<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery',
        'entry',
        'description'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'updated_at'
    ];

    public function clients()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function movies()
    {
        return $this->belongsTo(Movie::class, 'movie_id', 'id');
    }
}
