<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $today = Carbon::today();

        $panierSession = session()->get('cart', []); 
        $panierResorts = collect();

        if (!empty($panierSession)) {
            $ids = is_array($panierSession) ? array_keys($panierSession) : [];
            
            if(!empty($ids)) {
                $panierResorts = DB::table('resort')
                    ->join('pays', 'resort.codepays', '=', 'pays.codepays')
                    ->whereIn('resort.numresort', $ids)
                    ->select('resort.*', 'pays.nompays')
                    ->get();
            }
        }

        $reservations = DB::table('reservation')
            ->join('resort', 'reservation.numresort', '=', 'resort.numresort') 
            ->join('pays', 'resort.codepays', '=', 'pays.codepays')
            ->join('choisir', 'reservation.numreservation', '=', 'choisir.numreservation')
            ->join('typechambre', 'choisir.numtype', '=', 'typechambre.numtype')
            ->where('reservation.user_id', $userId)
            ->select(
                'reservation.*', 
                'resort.nomresort', 
                'resort.numresort',
                'pays.nompays',
                'typechambre.nomtype as type_chambre'
            )
            ->orderBy('reservation.datedebut', 'desc')
            ->get();

        $enCours = $reservations->filter(function ($res) use ($today) {
            return $today->between($res->datedebut, $res->datefin);
        });

        $aVenir = $reservations->filter(function ($res) use ($today) {
            return $res->datedebut > $today;
        });

        $terminees = $reservations->filter(function ($res) use ($today) {
            return $res->datefin < $today;
        });

        return view('reservations', [
            'panierResorts' => $panierResorts,
            'enCours' => $enCours,
            'aVenir' => $aVenir,
            'terminees' => $terminees
        ]);
    }
}