<div class="mb-4">
    <label for="name" class="block font-medium mb-2">Nom de la sonde</label>
    <input type="text" name="name" id="name" value="{{ old('name', $probe->name ?? '') }}"
           placeholder="Ex: Site vitrine, API de paiement..."
           class="w-full border rounded-lg p-3 @error('name') border-red-500 @enderror">
    @error('name')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror 
</div>

<div class="mb-4">
    <label class="block font-medium mb-2">Type de vérification</label>
    <select name="type" id="type" x-data x-model="type"
            class="w-full border rounded-lg p-3 @error('type') border-red-500 @enderror">
        <option value="http" {{ old('type', $probe->type ?? '') === 'http' ? 'selected' : '' }}>HTTP (site web)</option>
        <option value="tcp" {{ old('type', $probe->type ?? '') === 'tcp' ? 'selected' : '' }}>TCP (host:port)</option>
    </select>
    @error('type')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror 
</div>

<div class="mb-4">
    <label for="target" class="block font-medium mb-2">Cible</label>
    <input type="text" name="target" id="target" value="{{ old('target', $probe->target ?? '') }}"
           placeholder="https://example.com ou exemple.com:5432"
           class="w-full border rounded-lg p-3 @error('target') border-red-500 @enderror">
    <p class="text-xs text-gray-500 mt-1">Une URL complète pour HTTP, ou "host:port" pour TCP.</p>
    @error('target')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="check_interval_minutes" class="block font-medium mb-2">Intervalle de vérification (minutes)</label>
    <input type="number" name="check_interval_minutes" id="check_interval_minutes" min="5" max="1440"
           value="{{ old('check_interval_minutes', $probe->check_interval_minutes ?? 5) }}"
           class="w-full border rounded-lg p-3 @error('check_interval_minutes') border-red-500 @enderror">
    @error('check_interval_minutes')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror 
</div>

@if (isset($probe))
    <div class="mb-4 flex items-center gap-2">
        <input type="checkbox" name="is_active" id="is_active" value="1"
               {{ old('is_active', $probe->is_active) ? 'checked' : '' }} class="w-5 h-5">
        <label for="is_active">Sonde active</label>
    </div>
@endif 