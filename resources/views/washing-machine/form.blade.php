<x-crud.form-group name="name" label="Name" type="text" :value="$washingMachine->name ?? old('name')" placeholder="Enter Name" />
<x-crud.form-group name="photo" label="Photo" type="text" :value="$washingMachine->photo ?? old('photo')" placeholder="Enter Photo" />
<x-crud.form-group name="maximum_weight" label="Maximum Weight" type="number" :value="$washingMachine->maximum_weight ?? old('maximum_weight')" placeholder="Enter Maximum Weight" />
<x-crud.form-group 
    name="service_status" 
    label="สถานะเครื่อง" 
    type="radio" 
    :value="$washingMachine->service_status ?? 'available'" 
    :options="['available' => '✅ พร้อมใช้งาน', 'broken' => '🔧 เสีย/ซ่อม']" 
/>

<div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
    <x-ui.button type="submit" variant="primary" icon="fa fa-save">
        {{ isset($washingMachine->id) ? 'Update' : 'Create' }} Washing Machine
    </x-ui.button>
    <x-ui.button :href="route('washing-machines.index')" variant="secondary" icon="fa fa-times">Cancel</x-ui.button>
</div>