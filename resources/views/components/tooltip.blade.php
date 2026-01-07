@props(['text', 'position' => 'top'])

<div class="inline-block relative group">
    <span class="cursor-help inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-blue-500 rounded-full hover:bg-blue-600 transition-colors">
        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
        </svg>
    </span>
    
    <div class="absolute z-50 invisible group-hover:visible opacity-0 group-hover:opacity-100 transition-all duration-200 
        @if($position === 'top') bottom-full left-1/2 -translate-x-1/2 mb-2
        @elseif($position === 'bottom') top-full left-1/2 -translate-x-1/2 mt-2
        @elseif($position === 'left') right-full top-1/2 -translate-y-1/2 mr-2
        @elseif($position === 'right') left-full top-1/2 -translate-y-1/2 ml-2
        @endif">
        
        <div class="bg-gray-900 text-white text-sm rounded-lg shadow-lg px-4 py-3 max-w-xs whitespace-normal">
            {{ $text }}
            
            {{-- Flèche du tooltip --}}
            <div class="absolute w-2 h-2 bg-gray-900 rotate-45
                @if($position === 'top') -bottom-1 left-1/2 -translate-x-1/2
                @elseif($position === 'bottom') -top-1 left-1/2 -translate-x-1/2
                @elseif($position === 'left') -right-1 top-1/2 -translate-y-1/2
                @elseif($position === 'right') -left-1 top-1/2 -translate-y-1/2
                @endif">
            </div>
        </div>
    </div>
</div>
