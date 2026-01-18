<x-crud.form-group name="code" label="Code" :value="$employee->code ?? old('code')" placeholder="Enter employee code" required />
<x-crud.form-group name="name" label="Name" :value="$employee->name ?? old('name')" placeholder="Enter employee name" required />
<x-crud.form-group name="photo" label="Photo URL" :value="$employee->photo ?? old('photo')" placeholder="Photo path or URL" />
<div class="mb-4">
    <label for="image_upload" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Upload Photo</label>
    <input type="file" name="image_upload" id="image_upload" accept="image/*" class="w-full px-4 py-2.5 border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/50 dark:file:text-indigo-300 {{ $errors->has('image_upload') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}">
    @error('image_upload')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
</div>
<x-crud.form-group name="department_id" label="Department" type="select" :value="$employee->department_id ?? old('department_id')" :options="App\Models\Department::pluck('name', 'id')->toArray()" />
<div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
    <x-ui.button type="submit" variant="primary" icon="fa fa-save">{{ isset($employee->id) ? 'Update' : 'Create' }} Employee</x-ui.button>
    <x-ui.button :href="route('employees.index')" variant="secondary" icon="fa fa-times">Cancel</x-ui.button>
</div>