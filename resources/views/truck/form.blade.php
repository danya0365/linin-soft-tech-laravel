<x-crud.form-group name="name" label="Name" type="text" :value="$truck->name ?? old('name')" placeholder="Enter Name" />
<x-crud.form-group name="photo" label="Photo" type="text" :value="$truck->photo ?? old('photo')" placeholder="Enter Photo" />
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