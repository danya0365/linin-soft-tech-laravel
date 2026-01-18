<x-crud.form-group name="message" label="Message" type="textarea" :value="$note->message ?? old('message')" placeholder="Enter Message" />
<x-crud.form-group name="image_url" label="Image Url" type="text" :value="$note->image_url ?? old('image_url')" placeholder="Enter Image Url" />
<x-crud.form-group name="cost" label="Cost" type="number" :value="$note->cost ?? old('cost')" placeholder="Enter Cost" />
<x-crud.form-group name="tags" label="Tags" type="text" :value="$note->tags ?? old('tags')" placeholder="Enter Tags" />
<x-crud.form-group name="washing_machine_id" label="Washing Machine Id" type="select" :value="$note->washing_machine_id ?? old('washing_machine_id')" placeholder="Enter Washing Machine Id" />
<x-crud.form-group name="dryer_machine_id" label="Dryer Machine Id" type="select" :value="$note->dryer_machine_id ?? old('dryer_machine_id')" placeholder="Enter Dryer Machine Id" />
<x-crud.form-group name="truck_id" label="Truck Id" type="select" :value="$note->truck_id ?? old('truck_id')" placeholder="Enter Truck Id" />

<div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
    <x-ui.button type="submit" variant="primary" icon="fa fa-save">
        {{ isset($note->id) ? 'Update' : 'Create' }} Note
    </x-ui.button>
    <x-ui.button :href="route('notes.index')" variant="secondary" icon="fa fa-times">Cancel</x-ui.button>
</div>