<div>
    <label class="label">Name</label>
    <input type="text" name="name" value="{{ old('name', $facility->name ?? '') }}" required class="input">
</div>
<div>
    <label class="label">Description</label>
    <textarea name="description" rows="3" class="input">{{ old('description', $facility->description ?? '') }}</textarea>
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="label">Location</label>
        <input type="text" name="location" value="{{ old('location', $facility->location ?? '') }}" class="input">
    </div>
    <div>
        <label class="label">Capacity</label>
        <input type="number" name="capacity" value="{{ old('capacity', $facility->capacity ?? '') }}" class="input">
    </div>
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="label">Type</label>
        <select name="facility_type" class="input">
            @foreach (['conference_room','meeting_room','training_room','function_room','other'] as $type)
                <option value="{{ $type }}" @selected(old('facility_type', $facility->facility_type ?? 'meeting_room') === $type)>{{ str($type)->headline() }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="label">Status</label>
        <select name="status" class="input">
            @foreach (['available','maintenance','unavailable','archived'] as $status)
                <option value="{{ $status }}" @selected(old('status', $facility->status ?? 'available') === $status)>{{ str($status)->headline() }}</option>
            @endforeach
        </select>
    </div>
</div>
