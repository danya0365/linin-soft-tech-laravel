{{-- Washing Machine Form Fields --}}

<x-crud.form-group 
    name="name"
    label="Name"
    :value="$washingMachine->name ?? old('name')"
    placeholder="Enter washing machine name"
    required
/>

<x-crud.form-group 
    name="photo"
    label="Photo URL"
    :value="$washingMachine->photo ?? old('photo')"
    placeholder="Enter photo URL or path"
    help="Enter the URL or file path for the washing machine photo"
/>

<x-crud.form-group 
    name="maximum_weight"
    label="Maximum Weight (kg)"
    type="number"
    :value="$washingMachine->maximum_weight ?? old('maximum_weight')"
    placeholder="Enter maximum weight capacity"
    required
/>

{{-- Form Actions --}}
<div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
    <x-ui.button 
        type="submit"
        variant="primary"
        icon="fa fa-save"
    >
        {{ isset($washingMachine->id) ? 'Update' : 'Create' }} Washing Machine
    </x-ui.button>
    
    <x-ui.button 
        :href="route('washing-machines.index')"
        variant="secondary"
        icon="fa fa-times"
    >
        Cancel
    </x-ui.button>
</div>