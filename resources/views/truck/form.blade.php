{{-- Truck Form Fields --}}

<x-crud.form-group 
    name="name"
    label="Name"
    :value="$truck->name ?? old('name')"
    placeholder="Enter truck name"
    required
/>

<x-crud.form-group 
    name="photo"
    label="Photo URL"
    :value="$truck->photo ?? old('photo')"
    placeholder="Enter photo URL or path"
    help="Enter the URL or file path for the truck photo"
/>

<x-crud.form-group 
    name="plate_number"
    label="Plate Number"
    :value="$truck->plate_number ?? old('plate_number')"
    placeholder="Enter license plate number"
    required
/>

<x-crud.form-group 
    name="operation_id"
    label="Operation ID"
    :value="$truck->operation_id ?? old('operation_id')"
    placeholder="Enter operation ID"
    help="Optional operation identifier for this truck"
/>

{{-- Form Actions --}}
<div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
    <x-ui.button 
        type="submit"
        variant="primary"
        icon="fa fa-save"
    >
        {{ isset($truck->id) ? 'Update' : 'Create' }} Truck
    </x-ui.button>
    
    <x-ui.button 
        :href="route('trucks.index')"
        variant="secondary"
        icon="fa fa-times"
    >
        Cancel
    </x-ui.button>
</div>