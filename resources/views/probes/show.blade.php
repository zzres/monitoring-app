<x-app-layout>
    <div class="max-w-4xl mx-auto px-8">
        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif 
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 mb-6">
            <div class="min-w-0">
                <h1 class="text-2xl font-bold break-words">{{ $probe->name }}</h1>
                <p class="text-gray-500 break-all">{{ strtoupper($probe->type) }} · {{ $probe->target }}</p>
            </div>
            <div class="flex gap-2 flex-shrink-0">
                <form action="{{ route('probes.check', $probe) }}" method="POST">
                    @csrf 
                    <button type="submit" class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700">
                        Vérifier maitenant
                    </button>
                </form>
                <a href="{{ route('probes.edit', $probe) }}" class="px-4 py-2 rounded border hover:bg-gray-50">Modifier</a>
                <form action="{{ route('probes.destroy', $probe) }}" method="POST"
                      onsubmit="return confirm('Supprimer cette sonde ?')">
                    @csrf 
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 rounded border-red-300 text-red-600 hover:bg-red-50">
                        Supprimer 
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-5">
                <p class="text-sm text-gray-500">Statut actuel</p>
                <p class="text-2xl font-bold mt-1 {{ $probe->latestCheck?->is_up ? 'text-green-600' : 'text-red-600' }}">
                    {{ $probe->latestCheck?->is_up ? 'En ligne' : 'Hors ligne' }}
                </p>
            </div>
            <div class="bg-white rounded-lg shadow p-5">
                <p class="text-sm text-gray-500">Disponibilité (50 derniers checks)</p>
                <p class="text-2xl font-bold mt-1">{{ $uptimePercentage }}%</p>
            </div>
            <div class="bg-white rounded-lg shadow p-5">
                <p class="text-sm text-gray-500">Dernière vérification</p>
                <p class="text-2xl font-bold mt-1">
                    {{ $probe->latestcheck?->checked_at->diffForHumans() ?? '—' }}
                </p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="font-semibold mb-4">Temps de réponse (ms)</h2>
            <div class="relative" style="height: 250px;">
                <canvas id="responseTimeChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="w-full text-left min-w-[500px]">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-sm font-semibold text-gray-600">Date</th>
                        <th class="px-6 py-3 text-sm font-semibold text-gray-600">Statut</th>
                        <th class="px-6 py-3 text-sm font-semibold text-gray-600">Temps de réponse</th>
                        <th class="px-6 py-3 text-sm font-semibold text-gray-600">Détail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($checks as $check)
                        <tr class="border-b last:border-0">
                            <td class="px-6 py-4 text-sm">{{ $check->checked_at->format('d/m/Y H:i:s') }}</td>
                            <td class="px-6 py-4">
                                <span class="text-xs px-2 py-1 rounded-full {{ $check->is_up ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $check->is_up ? 'Up' : 'Down' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">{{ $check->response_time_ms ? $check->response_time_ms . ' ms' : '—' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $check->error_message ?? ($check->status_code ? "Code {$check->status_code}" : '—') }}</td>
                        </tr>
                    @empty 
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">Aucune vérification pour l'instant.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const checksData = {{  Illuminate\Support\Js::from($checks->reverse()->values()) }};

        const labels = checksData.map(c => {
            const date =new Date(c.checked_at);
            return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        });

        const responseTimes = checksData.map(c => c.response_time_ms);

        new Chart(document.getElementById('responseTimeChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Temps de réponse (ms)',
                    data: responseTimes,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    tension: 0.3,
                    fill: true,
                    spanGaps: true,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>