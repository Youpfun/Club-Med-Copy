<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Resort extends Model
{
    use HasFactory;

    protected $table = 'resort';
    protected $primaryKey = 'numresort';
    public $timestamps = false;

    protected $fillable = [
        'codepays', 'numdomaine', 'numdocumentation', 'nomresort', 
        'descriptionresort', 'nbchambrestotal', 'nbtridents', 
        'latituderesort', 'longituderesort', 'est_valide'
    ];

    // --- RELATIONS EXISTANTES ---
    public function pays() { return $this->belongsTo(Pays::class, 'codepays', 'codepays'); }
    public function domaineskiable() { return $this->belongsTo(DomaineSkiable::class, 'numdomaine', 'numdomaine'); }
    public function documentation() { return $this->belongsTo(Documentation::class, 'numdocumentation', 'numdocumentation'); }
    public function photos() { return $this->hasMany(Photo::class, 'numresort', 'numresort'); }
    public function restaurants() { return $this->hasMany(Restaurant::class, 'numresort', 'numresort'); }
    public function regroupements() { return $this->belongsToMany(RegroupementClub::class, 'appartenir', 'numresort', 'numregroupement'); }
    public function typechambres() { return $this->belongsToMany(TypeChambre::class, 'proposer', 'numresort', 'numtype')->withPivot('nbchambres'); }
    public function tarifs() { return $this->hasMany(Tarifer::class, 'numresort', 'numresort'); }
    public function avis() { return $this->hasMany(Avis::class, 'numresort', 'numresort'); }
    public function typeclubs() { return $this->belongsToMany(Typeclub::class, 'classer', 'numresort', 'numtypeclub'); }
    public function localisations() { return $this->belongsToMany(Localisation::class, 'situer2', 'numresort', 'numlocalisation'); }
    
    // Relation générale (Types d'activités disponibles)
    public function typesActivites() { return $this->belongsToMany(TypeActivite::class, 'partager', 'numresort', 'numtypeactivite'); }

    /**
     * NOUVELLE MÉTHODE : Récupère le partenaire lié au Resort (par convention de nom)
     */
    public function partenaire()
    {
        // On cherche le partenaire dont le nom est "Service [NomResort]"
        return Partenaire::where('nompartenaire', 'Service ' . $this->nomresort)->first();
    }

    /**
     * NOUVELLE MÉTHODE : Récupère les activités SPÉCIFIQUES configurées pour ce resort
     */
    public function activitesSpecifiques()
    {
        $partner = $this->partenaire();
        
        if (!$partner) {
            return collect();
        }

        return DB::table('activite')
            ->join('fourni', 'activite.numactivite', '=', 'fourni.numactivite')
            ->join('typeactivite', 'activite.numtypeactivite', '=', 'typeactivite.numtypeactivite')
            ->where('fourni.numpartenaire', $partner->numpartenaire)
            ->select(
                'activite.*', 
                'typeactivite.nomtypeactivite', 
                'fourni.est_incluse as pivot_inclus', // Valeur spécifique au resort
                'fourni.prix as pivot_prix'           // Prix spécifique au resort
            )
            ->get();
    }
}