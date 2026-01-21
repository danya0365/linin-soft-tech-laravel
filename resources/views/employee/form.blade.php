<x-crud.form-group name="code" label="Code" type="text" :value="$employee->code ?? old('code')" placeholder="Enter Code" />
<x-crud.form-group name="name" label="Name" type="text" :value="$employee->name ?? old('name')" placeholder="Enter Name" />
<x-crud.form-group name="photo" label="Photo" type="text" :value="$employee->photo ?? old('photo')" placeholder="Enter Photo" />

{{-- Image Upload --}}
<div class="mb-4">
    <label for="image_upload" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Upload Photo</label>
    <input 
        type="file" 
        name="image_upload" 
        id="image_upload"
        accept="image/*"
        class="w-full px-4 py-2.5 border rounded-lg transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent"
    >
    @error('image_upload')
        <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
            <i class="fa fa-exclamation-circle"></i>
            <span>{{ $message }}</span>
        </p>
    @enderror
</div>

{{-- Department Select --}}
@php
    $departmentOptions = [];
    foreach (\App\Models\Department::get() as $department) {
        $departmentOptions[$department->id] = $department->name;
    }
@endphp
<x-crud.form-group 
    name="department_id" 
    label="Department" 
    type="select" 
    :value="$employee->department_id ?? old('department_id')" 
    placeholder="เลือก"
    :options="$departmentOptions" 
/>

<div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
    <x-ui.button type="submit" variant="primary" icon="fa fa-save">
        {{ isset($employee->id) ? 'Update' : 'Create' }} Employee
    </x-ui.button>
    <x-ui.button :href="route('employees.index')" variant="secondary" icon="fa fa-times">Cancel</x-ui.button>
</div>