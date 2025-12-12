<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Choisir extends Model
{
    use HasFactory;

    protected $table = 'choisir';
    public $timestamps = false;

    protected $fillable = [
        'numreservation',
        'numtype',
    ];

    public function typechambre()
    {
        return $this->belongsTo(TypeChambre::class, 'numtype', 'numtype');
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'numreservation', 'numreservation');
    }
}
