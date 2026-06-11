@php
    $linenTypeOptions = [];
    foreach (\App\Models\LinenType::get() as $linenType) {
        $linenTypeOptions[$linenType->id] = $linenType->name;
    }
@endphp
<x-crud.form-group 
    name="linen_type_id" 
    label="Linen Type" 
    type="select" 
    :value="$linenProduct->linen_type_id ?? old('linen_type_id')" 
    placeholder="เลือก"
    :options="$linenTypeOptions" 
/>
<x-crud.form-group name="name" label="Name" type="text" :value="$linenProduct->name ?? old('name')" placeholder="Enter Name" />

<div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
    <x-ui.button type="submit" variant="primary" icon="fa fa-save">
        {{ isset($linenProduct->id) ? 'Update' : 'Create' }} Linen Product
    </x-ui.button>
    <x-ui.button :href="route('linen-products.index')" variant="secondary" icon="fa fa-times">Cancel</x-ui.button>
</div>