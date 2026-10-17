<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Tableau de bord</h1>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-5">
                <p class="text-sm text-gray-500">Sondes surveillées</p>
                <p class="text-3xl font-bold mt-1">{{ $total }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-5">
                <p class="text-sm text-gray-500">En ligne</p>
                <p class="text-3xl font-bold mt-1 text-green-600">{{ $up }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-5">
                <p class="text-sm text-gray-500">Hors ligne</p>
                <p class="text-3xl font-bold mt-1 {{ $down > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ $down }}</p>
            </div>
        </div>

        @if ($downProbes->isNotEmpty())
            <div class="bg-red-50 border border-red-200 rounded-lg p-5 mb-8">
                <h2 class="font-semibold text-red-700 mb-3">⚠️ Sondes actuellement hors ligne</h2>
                <div class="space-y-2">
                    @foreach ($downProbes as $probe)
                        <a href="{{ route('probes.show', $probe) }}" class="flex justify-between items-center bg-white rounded p-3 hover:shadow-sm transition">
                            <span class="font-medium">{{ $probe->name }}</span>
                            <span class="text-sm text-gray-500">{{ $probe->latestCheck->checked_at->diffForHumans() }}</span>
                        </a>
                    @endforeach 
                </div>
            </div>
        @endif 

        <div class="flex justify-between items-center mb-4">
            <h2 class="font-semibold">Toutes les sondes</h2>
            <a href="{{ route('probes.create') }}" class="tex-sm text-indigo-600 hover:underline">+ Ajouter une sonde</a>
        </div>

        @if ($probes->isEmpty())
            <div class="text-center py-12 text-gray-500 bg-white rounded-lg shadow">
                <p>Tu ne surveilles encore aucune sonde.</p>
                <a href="{{ route('probes.create') }}" class="text-indigo-600 hover:underline">Ajouter ta première sonde</a>
            </div>
        @else 
            <div class="space-y-3">
                @foreach ($probes as $probe)
                    <a href="{{ route('probes.show', $probe) }}"
                       class="flex items-center justify-between bg-white rounded-lg shadow p-5 hover:shadow-md transition {{ !$probe->is_active ? 'opacty-50' : '' }}">
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
