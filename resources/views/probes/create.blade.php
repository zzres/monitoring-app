<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Nouvelle sonde</h1>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('probes.store') }}" method="POST">
                @csrf 
                @include('probes._form')

                <div class="flex gap-3 mt-6">
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded hover:bg-indigo-700">
                        Créer
                    </button>
                    <a href="{{ route('probes.index') }}" class="px-6 py-3 rounded border hover:bg-gray-50">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>