<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conflit d'Indisponibilité - Club Med</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <main class="py-12 px-4 lg:px-8">
        <div class="max-w-4xl mx-auto">
            
            <div class="bg-white rounded-xl shadow-lg border-l-8 border-red-500 overflow-hidden">
                <div class="p-8">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="ml-6 w-full">
                            <h2 class="text-2xl font-bold text-gray-900 font-serif">Attention : Risque de Surbooking</h2>
                            <p class="mt-2 text-gray-600">
                                Vous tentez de bloquer une chambre pour la période du 
                                <strong>{{ \Carbon\Carbon::parse($input['datedebut'])->format('d/m/Y') }}</strong> au 
                                <strong>{{ \Carbon\Carbon::parse($input['datefin'])->format('d/m/Y') }}</strong>.
                            </p>
                            <p class="mt-2 text-red-600 font-semibold">
                                Cela réduirait la capacité disponible à {{ $conflits['total_chambres'] - $conflits['chambres_bloquees_apres'] }} chambre(s), 
                                mais vous avez actuellement {{ $conflits['total_reserve'] }} réservation(s) active(s) sur ce type de chambre.
                            </p>
                        </div>
                    </div>

                    <div class="mt-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Réservations occupantes / impactées :</h3>
                        <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Réservation</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Client</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Dates</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($conflits['reservations'] as $res)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                #{{ $res->numreservation }}
                                                <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $res->statut === 'Confirmée' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                    {{ $res->statut }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $res->user->name ?? 'Client inconnu' }} <br>
                                                <span class="text-xs">{{ $res->user->email ?? '' }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($res->datedebut)->format('d/m') }} - 
                                                {{ \Carbon\Carbon::parse($res->datefin)->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                {{-- Lien vers l'action de modification (ici proposition d'alternative) --}}
                                                <a href="{{ route('vente.propose-alternative-form', $res->numreservation) }}" target="_blank" class="text-blue-600 hover:text-blue-900 font-bold hover:underline">
                                                    Gérer / Changer →
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end space-x-4">
                        <a href="{{ route('marketing.indisponibilite.create', ['numresort' => DB::table('proposer')->where('numtype', App\Models\Chambre::find($input['idchambre'])->numtype)->value('numresort') ?? 1]) }}" 
                           class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold rounded-full transition">
                            Annuler
                        </a>

                        <form action="{{ route('marketing.indisponibilite.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="idchambre" value="{{ $input['idchambre'] }}">
                            <input type="hidden" name="datedebut" value="{{ $input['datedebut'] }}">
                            <input type="hidden" name="datefin" value="{{ $input['datefin'] }}">
                            <input type="hidden" name="motif" value="{{ $input['motif'] }}">
                            {{-- Champ caché pour forcer la création malgré le conflit --}}
                            <input type="hidden" name="force" value="1">
                            
                            <button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-full shadow-md transition transform hover:scale-105">
                                Forcer le blocage (Créer surbooking)
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </main>
</body>
</html>