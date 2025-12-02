<header class="flex items-center justify-between gap-x-4 overflow-x-clip bg-white p-4 px-4 lg:px-8 relative isolate z-5 border-b border-gray-100" role="banner">
    <a href="{{ url('/') }}" class="w-32 md:w-40">
        <span class="sr-only">Club Med Luxury All Inclusive Resorts & Holiday Packages</span>
        <span class="text-2xl font-bold text-blue-700">Club Med</span>
    </a>
    <nav class="hidden md:flex items-center gap-x-6 px-4">
        <a href="{{ url('/resorts') }}" class="text-sm font-semibold text-gray-900 hover:text-blue-600 transition-colors">Nos Resorts</a>
        <a href="{{ url('/clients') }}" class="text-sm font-semibold text-gray-900 hover:text-blue-600 transition-colors">Clients</a>
        <a href="{{ url('/typeclubs') }}" class="text-sm font-semibold text-gray-900 hover:text-blue-600 transition-colors">Types de clubs</a>
        <a href="{{ url('/localisations') }}" class="text-sm font-semibold text-gray-900 hover:text-blue-600 transition-colors">Localisations</a>
    </nav>
    <div class="flex gap-x-4">
        <a href="{{ url('/resorts') }}" class="flex items-center justify-center px-4 py-2 md:px-6 md:py-2.5 bg-yellow-500 hover:bg-yellow-600 text-black rounded-full font-semibold text-sm transition-colors">
            <span class="hidden md:inline">Nos Offres</span>
            <span class="md:hidden text-xl">%</span>
        </a>
    </div>
</header>
