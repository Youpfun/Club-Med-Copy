<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Avis;
use App\Models\Photo;
use Carbon\Carbon;

class AvisController extends Controller
{
    public function create($idReservation)
    {
        $userId = Auth::id();

        $reservation = DB::table('reservation')
            ->join('resort', 'reservation.numresort', '=', 'resort.numresort')
            ->where('reservation.numreservation', $idReservation)
            ->where('reservation.user_id', $userId)
            ->select('reservation.*', 'resort.nomresort', 'resort.numresort')
            ->first();

        if (!$reservation) {
            return redirect()->route('reservations.index')
                ->with('error', 'Réservation introuvable ou accès non autorisé.');
        }

        return view('avis', ['reservation' => $reservation]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'numresort'   => 'required|integer',
            'note'        => 'required|integer|min:1|max:5',
            'commentaire' => 'required|string|max:1000',
            'photo'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', 
        ]);

        $avis = Avis::create([
            'user_id'         => Auth::id(),
            'numresort'       => $request->numresort,
            'noteavis'        => $request->note,
            'commentaire'     => $request->commentaire,
            'datepublication' => Carbon::now(),
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = 'avis_' . $avis->numavis . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('avis_photos', $filename, 'public');

            Photo::create([
                'numresort'          => $request->numresort,
                'numavis'            => $avis->numavis,
                'nomfichierphoto'    => $file->getClientOriginalName(),
                'cheminfichierphoto' => 'storage/' . $path, 
                'formatphoto'        => $file->getClientOriginalExtension(),
                'taillephoto'        => $file->getSize(),
            ]);
        }

        return redirect()->route('reservations.index')
            ->with('success', 'Merci ! Votre avis a été publié avec succès.');
    }
}