<x-app-layout>
    <!-- TITRE DE LA PAGE (S'affiche sous ton header) -->
    <x-slot name="header">
        <h2 class="font-extrabold text-3xl text-[#113559] leading-tight flex items-center uppercase tracking-wide">
            <span class="mr-3 text-[#ffcc00] text-4xl">Ψ</span>
            {{ __('Mon Espace Personnel') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">
            
            <!-- Message de succès -->
            @if (session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 shadow-sm rounded-r flex items-center animate-pulse">
                    <svg class="h-6 w-6 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <p class="text-sm text-green-800 font-bold">{{ session('success') }}</p>
                </div>
            @endif

            <!-- BLOC INFORMATIONS PERSONNELLES -->
            <div class="bg-white shadow-xl rounded-none overflow-hidden border-t-4 border-[#113559]">
                <div class="p-8">
                    <div class="flex items-center justify-between mb-8 border-b border-gray-100 pb-4">
                        <div>
                            <h3 class="text-xl font-bold text-[#113559] uppercase tracking-wider">Mes Coordonnées</h3>
                            <p class="text-sm text-gray-500 mt-1">Gérez votre identité pour vos prochains voyages.</p>
                        </div>
                    </div>

                    <form action="{{ route('user.update.custom') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Champs (Layout pleine largeur sans la photo) -->
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-bold text-[#113559] uppercase mb-2">Prénom</label>
                                    <input type="text" name="prenom" value="{{ old('prenom', explode(' ', Auth::user()->name)[0] ?? '') }}" class="w-full border-gray-300 rounded-sm focus:border-[#113559] focus:ring-[#113559] py-3">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-[#113559] uppercase mb-2">Nom</label>
                                    <input type="text" name="nom" value="{{ old('nom', explode(' ', Auth::user()->name, 2)[1] ?? '') }}" class="w-full border-gray-300 rounded-sm focus:border-[#113559] focus:ring-[#113559] py-3">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-bold text-[#113559] uppercase mb-2">E-mail</label>
                                    <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" class="w-full border-gray-300 bg-gray-50 rounded-sm focus:border-[#113559] focus:ring-[#113559] py-3">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-[#113559] uppercase mb-2">Téléphone</label>
                                    <input type="tel" name="telephone" value="{{ old('telephone', Auth::user()->telephone) }}" class="w-full border-gray-300 rounded-sm focus:border-[#113559] focus:ring-[#113559] py-3">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-[#113559] uppercase mb-2">Date de naissance</label>
                                @php
                                    $dateValue = '';
                                    if (Auth::user()->datenaissance) {
                                        try {
                                            $dateValue = \Carbon\Carbon::parse(Auth::user()->datenaissance)->format('Y-m-d');
                                        } catch (\Exception $e) { $dateValue = Auth::user()->datenaissance; }
                                    }
                                @endphp
                                <input type="date" name="datenaissance" value="{{ old('datenaissance', $dateValue) }}" class="w-full md:w-1/2 border-gray-300 rounded-sm focus:border-[#113559] focus:ring-[#113559] py-3 text-gray-600">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-[#113559] uppercase mb-2">Adresse</label>
                                <div class="flex gap-4 mb-4">
                                    <input type="number" name="numrue" placeholder="N°" value="{{ old('numrue', Auth::user()->numrue) }}" class="w-20 border-gray-300 rounded-sm focus:border-[#113559] focus:ring-[#113559] py-3">
                                    <input type="text" name="nomrue" placeholder="Nom de la rue" value="{{ old('nomrue', Auth::user()->nomrue) }}" class="flex-1 border-gray-300 rounded-sm focus:border-[#113559] focus:ring-[#113559] py-3">
                                </div>
                                <div class="flex gap-4">
                                    <input type="text" name="codepostal" placeholder="Code Postal" value="{{ old('codepostal', Auth::user()->codepostal) }}" class="w-32 border-gray-300 rounded-sm focus:border-[#113559] focus:ring-[#113559] py-3">
                                    <input type="text" name="ville" placeholder="Ville" value="{{ old('ville', Auth::user()->ville) }}" class="flex-1 border-gray-300 rounded-sm focus:border-[#113559] focus:ring-[#113559] py-3">
                                </div>
                            </div>

                            <div class="pt-4 text-right">
                                <button type="submit" class="bg-[#ffcc00] hover:bg-[#e6b800] text-[#113559] font-bold py-3 px-8 rounded-full shadow-md uppercase text-sm tracking-widest transition-transform transform hover:scale-105">
                                    Enregistrer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- BLOC MOT DE PASSE (Sans 2FA affiché, car géré par Jetstream) -->
            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div class="bg-white shadow-xl rounded-none overflow-hidden border-t-4 border-gray-400">
                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50">
                        <h3 class="text-lg font-bold text-[#113559] uppercase tracking-wide">
                            Mot de passe
                        </h3>
                    </div>
                    <div class="p-8">
                        @livewire('profile.update-password-form')
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>