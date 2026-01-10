<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Avis;
use App\Models\Photo;
use App\Models\Signalement;
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
                ->with('error', 'Reservation introuvable ou acces non autorise.');
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
            ->with('success', 'Merci ! Votre avis a ete publie avec succes.');
    }

    public function report(Request $request, $numavis)
    {
        $avis = Avis::find($numavis);
        if (!$avis) {
            return redirect()->back()
                ->with('error', 'Avis introuvable.');
        }

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        Signalement::create([
            'numresort' => $avis->numresort,
            'numavis' => $numavis,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'datesignalement' => Carbon::now(),
            'traite' => false,
        ]);

        return redirect()->back()
            ->with('success', 'Merci ! Votre signalement a ete enregistre. Nous allons examiner cet avis.');
    }

    public function repondre(Request $request, $numavis)
    {
        $avis = Avis::findOrFail($numavis);

        $request->validate([
            'reponse' => 'required|string|max:2000',
        ]);

        $avis->update([
            'reponse' => $request->reponse,
            'reponse_user_id' => Auth::id(),
            'date_reponse' => Carbon::now(),
        ]);

        return redirect()->back()
            ->with('success', 'Votre reponse a ete publiee avec succes.');
    }
}