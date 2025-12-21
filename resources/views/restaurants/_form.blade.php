@php
    $isEdit = isset($restaurant);
@endphp

<div>
    <label>Name</label>
    <input type="text" name="name" value="{{ old('name', $isEdit ? $restaurant->name : '') }}" required>
</div>

<div>
    <label>Email</label>
    <input type="email" name="email" value="{{ old('email', $isEdit ? $restaurant->email : '') }}" required>
</div>

<div>
    <label>Phone Number</label>
    <input type="text" name="phone_number" value="{{ old('phone_number', $isEdit ? $restaurant->phone_number : '') }}">
</div>

<div>
    <label>Address</label>
    <input type="text" name="address" value="{{ old('address', $isEdit ? $restaurant->address : '') }}" required>
</div>

<div>
    <label>Capacity</label>
    <input type="number" name="capacity" min="1" value="{{ old('capacity', $isEdit ? $restaurant->capacity : '') }}" required>
</div>

<div>
    <label>Description</label>
    <textarea name="description" required>{{ old('description', $isEdit ? $restaurant->description : '') }}</textarea>
</div>

<h3>Opening hours</h3>

@php
    $oh = $isEdit ? ($restaurant->opening_hours ?? []) : [];
@endphp

<div>
    <label>Monday</label>
    <input type="text" name="mon_hours" value="{{ old('mon_hours', isset($oh['mon']) ? implode(', ', (array) $oh['mon']) : '') }}">
</div>
<div>
    <label>Tuesday</label>
    <input type="text" name="tue_hours" value="{{ old('tue_hours', isset($oh['tue']) ? implode(', ', (array) $oh['tue']) : '') }}">
</div>
<div>
    <label>Wednesday</label>
    <input type="text" name="wed_hours" value="{{ old('wed_hours', isset($oh['wed']) ? implode(', ', (array) $oh['wed']) : '') }}">
</div>
<div>
    <label>Thursday</label>
    <input type="text" name="thu_hours" value="{{ old('thu_hours', isset($oh['thu']) ? implode(', ', (array) $oh['thu']) : '') }}">
</div>
<div>
    <label>Friday</label>
    <input type="text" name="fri_hours" value="{{ old('fri_hours', isset($oh['fri']) ? implode(', ', (array) $oh['fri']) : '') }}">
</div>
<div>
    <label>Saturday</label>
    <input type="text" name="sat_hours" value="{{ old('sat_hours', isset($oh['sat']) ? implode(', ', (array) $oh['sat']) : '') }}">
</div>
<div>
    <label>Sunday</label>
    <input type="text" name="sun_hours" value="{{ old('sun_hours', isset($oh['sun']) ? implode(', ', (array) $oh['sun']) : '') }}">
</div>

@error('opening_hours')
    <div class="error" style="color: red; margin-top: 5px;">
        {{ $message }}
    </div>
@enderror
