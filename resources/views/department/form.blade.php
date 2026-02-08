<x-crud.form-group name="var_name" label="Var Name" type="text" :value="$department->var_name ?? old('var_name')" placeholder="Enter Var Name" />
<x-crud.form-group name="name" label="Name" type="text" :value="$department->name ?? old('name')" placeholder="Enter Name" />
<x-crud.form-group name="input_unit" label="Input Unit" type="text" :value="$department->input_unit ?? old('input_unit')" placeholder="Enter Input Unit" />

<div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
    <x-ui.button type="submit" variant="primary" icon="fa fa-save">
        {{ isset($department->id) ? 'Update' : 'Create' }} Department
    </x-ui.button>
    <x-ui.button :href="route('departments.index')" variant="secondary" icon="fa fa-times">Cancel</x-ui.button>
</div>