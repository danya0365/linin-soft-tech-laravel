<x-crud.form-group name="department_id" label="Department Id" type="select" :value="$departmentDailyCostLog->department_id ?? old('department_id')" placeholder="Enter Department Id" />
<x-crud.form-group name="daily_date" label="Daily Date" type="date" :value="$departmentDailyCostLog->daily_date ?? old('daily_date')" placeholder="Enter Daily Date" />
<x-crud.form-group name="cost" label="Cost" type="number" :value="$departmentDailyCostLog->cost ?? old('cost')" placeholder="Enter Cost" />

<div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
    <x-ui.button type="submit" variant="primary" icon="fa fa-save">
        {{ isset($departmentDailyCostLog->id) ? 'Update' : 'Create' }} Department Daily Cost Log
    </x-ui.button>
    <x-ui.button :href="route('department-daily-cost-logs.index')" variant="secondary" icon="fa fa-times">Cancel</x-ui.button>
</div>