<x-crud.form-group name="name" label="Name" type="text" :value="$truck->name ?? old('name')" placeholder="Enter Name" />

{{-- Photo Input with Preview --}}
<div class="mb-4">
    <label for="photo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Photo</label>
    
    @if(isset($truck->photo) && $truck->photo)
        <div class="mb-3">
            <img src="{{ asset($truck->photo) }}" alt="Current Photo" class="h-32 w-auto object-cover rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
            <p class="text-xs text-gray-500 mt-1">Current photo</p>
        </div>
    @endif
    
    <input 
        type="file" 
        name="photo" 
        id="photo"
        accept="image/*"
        class="w-full px-4 py-2.5 border rounded-lg transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/50 dark:file:text-indigo-300 border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400"
    >
    @error('photo')
        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>

<x-crud.form-group name="plate_number" label="Plate Number" type="text" :value="$truck->plate_number ?? old('plate_number')" placeholder="Enter Plate Number" />
<x-crud.form-group name="operation_id" label="Operation Id" type="text" :value="$truck->operation_id ?? old('operation_id')" placeholder="Enter Operation Id" />
<x-crud.form-group 
    name="service_status" 
    label="สถานะเครื่อง" 
    type="radio" 
    :value="$truck->service_status ?? 'available'" 
    :options="['available' => '✅ พร้อมใช้งาน', 'broken' => '🔧 เสีย/ซ่อม']" 
/>

<div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
    <x-ui.button type="submit" variant="primary" icon="fa fa-save">
        {{ isset($truck->id) ? 'Update' : 'Create' }} Truck
    </x-ui.button>
    <x-ui.button :href="route('trucks.index')" variant="secondary" icon="fa fa-times">Cancel</x-ui.button>
</div>