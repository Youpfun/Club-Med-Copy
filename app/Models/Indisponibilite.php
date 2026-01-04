<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indisponibilite extends Model
{
    use HasFactory;

    protected $table = 'indisponibilite';
    protected $primaryKey = 'numindisponibilite';
    public $timestamps = false;

    protected $fillable = [
        'idchambre',
        'datedebut',
        'datefin',
        'motif'
    ];

    public function chambre()
    {
        return $this->belongsTo(Chambre::class, 'idchambre', 'idchambre');
    }
}