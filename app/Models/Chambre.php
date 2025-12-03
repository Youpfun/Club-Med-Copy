<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chambre extends Model
{
    use HasFactory;

    protected $table = 'chambre';
    protected $primaryKey = 'idchambre';
    public $timestamps = false;

    protected $fillable = [
        'numtype',
        'numchambre'
    ];

    /**
     * Relation avec le type de chambre
     */
    public function typechambre()
    {
        return $this->belongsTo(TypeChambre::class, 'numtype', 'numtype');
    }
}
