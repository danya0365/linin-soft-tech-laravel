

<div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
    <x-ui.button type="submit" variant="primary" icon="fa fa-save">
        {{ isset($loginHistory->id) ? 'Update' : 'Create' }} Login History
    </x-ui.button>
    <x-ui.button :href="route('login-histories.index')" variant="secondary" icon="fa fa-times">Cancel</x-ui.button>
</div>