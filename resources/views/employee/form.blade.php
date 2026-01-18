<x-crud.form-group name="code" label="Code" type="text" :value="$employee->code ?? old('code')" placeholder="Enter Code" />
<x-crud.form-group name="name" label="Name" type="text" :value="$employee->name ?? old('name')" placeholder="Enter Name" />
<x-crud.form-group name="photo" label="Photo" type="text" :value="$employee->photo ?? old('photo')" placeholder="Enter Photo" />
<x-crud.form-group name="department_id" label="Department Id" type="select" :value="$employee->department_id ?? old('department_id')" placeholder="Enter Department Id" />
<x-crud.form-group name="image_upload" label="Image Upload" type="text" :value="$employee->image_upload ?? old('image_upload')" placeholder="Enter Image Upload" />

<div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
    <x-ui.button type="submit" variant="primary" icon="fa fa-save">
        {{ isset($employee->id) ? 'Update' : 'Create' }} Employee
    </x-ui.button>
    <x-ui.button :href="route('employees.index')" variant="secondary" icon="fa fa-times">Cancel</x-ui.button>
</div>