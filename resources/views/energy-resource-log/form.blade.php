<x-crud.form-group name="energy_resource_id" label="Energy Resource Id" type="select" :value="$energyResourceLog->energy_resource_id ?? old('energy_resource_id')" placeholder="Enter Energy Resource Id" />
<x-crud.form-group name="employee_id" label="Employee Id" type="select" :value="$energyResourceLog->employee_id ?? old('employee_id')" placeholder="Enter Employee Id" />
<x-crud.form-group name="value" label="Value" type="text" :value="$energyResourceLog->value ?? old('value')" placeholder="Enter Value" />
<x-crud.form-group name="unit" label="Unit" type="text" :value="$energyResourceLog->unit ?? old('unit')" placeholder="Enter Unit" />
<x-crud.form-group name="lot_number" label="Lot Number" type="text" :value="$energyResourceLog->lot_number ?? old('lot_number')" placeholder="Enter Lot Number" />

<div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
    <x-ui.button type="submit" variant="primary" icon="fa fa-save">
        {{ isset($energyResourceLog->id) ? 'Update' : 'Create' }} Energy Resource Log
    </x-ui.button>
    <x-ui.button :href="route('energy-resource-log.index')" variant="secondary" icon="fa fa-times">Cancel</x-ui.button>
</div>