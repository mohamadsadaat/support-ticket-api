<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Priority extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];
    
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
