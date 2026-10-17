<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Mes sondes</h1>
            <a href="{{ route('probes.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                + Nouvelle sonde 
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif 

        @if ($probes->isEmpty())
            <div class="text-center py-12 text-gray-500">
                <p>Tu ne surveilles encore aucune sonde.</p>
                <a href="{{ route('probes.create') }}" class="text-indigo-600 hover:underline">Ajouter ta première sonde</a>
            </div>
        @else 
            <div class="space-y-3">
                @foreach ($probes as $probe)
                    <a href="{{ route('probes.show', $probe) }}"
                       class="flex items-center justify-between bg-white rounded-lg shadow p-5 hover:shadow-md transition {{ !$probe->is_active ? 'opacity-50' : '' }}">
                        <div class="flex items-center gap-4">
                            <span class="w-3 h-3 rounded-full
                                {{ !$probe->is_active ? 'bg-gray-400' : ($probe->latestCheck?->is_up ? 'bg-green-500' : ($probe->latestCheck ? 'bg-red-500' : 'bg-gray-300')) }}">
                            </span>
                            <div>
                                <p class="font-semibold">{{ $probe->name }}</p>
                                <p class="text-sm text-gray-500">{{ strtoupper($probe->type) }} · {{ $probe->target }}</p>
                            </div>
                        </div>

                        <div class="text-right text-sm text-gray-500">
                            @if ($probe->latestCheck)
                                {{ $probe->latestCheck->is_up ? 'En ligne' : 'Hors ligne' }}
                                <br>
                                {{ $probe->latestCheck->checked_at->diffForHumans() }}
                            @else 
                                Pas encore vérifiée 
                            @endif 
                        </div>
                    </a>
                @endforeach 
            </div>
        @endif 
    </div>
</x-app-layout>