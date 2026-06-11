@extends('layouts.app')

@section('template_title')
    Create Note 1
@endsection

@section('content')
    <div class="container mx-auto px-4 py-6 max-w-4xl">

        {{-- Breadcrumb --}}
        <x-crud.breadcrumb :items="[
            ['label' => 'Admin', 'route' => route('admin')],
            ['label' => 'Dryer Machines', 'route' => route('dryer-machines.index')],
            ['label' => $dryerMachine->name ?? '-', 'route' => route('dryer-machines.show', $dryerMachine->id)],
            ['label' => 'Add Note'],
        ]" />

        {{-- Form Card --}}
        <x-ui.card>
            <x-slot:header>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">เพิ่มบันทึก - Create Note</h2>
            </x-slot:header>

            <form action="{{ request()->url() }}" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="dryer_machine_id" value="{{ $dryerMachine->id }}">
                @csrf

                {{-- Tags Field with Suggestions --}}
                <div class="mb-4">
                    <label for="tags" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tags (คั่นด้วย comma เช่น ค่าซ่อม, ค่าอะไหล่)
                    </label>
                    <input type="text" name="tags" id="tags" value="{{ old('tags') }}"
                        placeholder="พิมพ์ tags คั่นด้วย comma เช่น ค่าซ่อม, ค่าอะไหล่, ค่าแรง" autocomplete="off"
                        class="w-full px-4 py-2.5 border rounded-lg transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent {{ $errors->has('tags') ? 'border-red-500 ring-2 ring-red-200' : 'border-gray-300 dark:border-gray-600' }}">

                    @if (count($existingTags) > 0)
                        <div class="mt-3">
                            <small class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-600">
                                Tags ที่เคยใช้ (คลิกเพื่อเพิ่ม):
                            </small>
                            <div class="flex flex-wrap gap-2 mt-2">
                                @foreach ($existingTags as $tag)
                                    <x-ui.badge variant="secondary"
                                        class="cursor-pointer hover:scale-110 transition-transform"
                                        onclick="addTag('{{ $tag }}')">
                                        #{{ $tag }}
                                    </x-ui.badge>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @error('tags')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                            <i class="fa fa-exclamation-circle"></i>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                {{-- Message Field --}}
                <x-crud.form-group name="message" label="บันทึกข้อความ - Note" type="textarea" :value="$note->message ?? old('message')"
                    rows="4" required />

                {{-- Cost Field --}}
                <x-crud.form-group name="cost" label="ค่าใช้จ่าย - Cost (Thai Baht)" type="number" :value="$note->cost ?? old('cost')"
                    placeholder="0.00" required />

                {{-- Image Upload with Crop --}}
                <x-image-crop-upload name="image_upload" label="อัพโหลดรูป - Attach Photo" />

                {{-- Note Date Field --}}
                <x-crud.form-group name="note_date" label="วันที่บันทึก" type="date" :value="old('note_date')" />

                {{-- Form Actions --}}
                <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <x-ui.button type="submit" variant="primary" icon="fa fa-save">
                        Submit
                    </x-ui.button>

                    <x-ui.button type="reset" variant="secondary">
                        Reset
                    </x-ui.button>

                    <x-ui.button :href="route('dryer-machines.show', $dryerMachine->id)" variant="outline" icon="fa fa-times">
                        Cancel
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>

    <script>
        function addTag(tag) {
            const input = document.getElementById('tags');
            const currentValue = input.value.trim();

            // Check if tag already exists
            const existingTags = currentValue.split(',').map(t => t.trim()).filter(t => t);
            if (existingTags.includes(tag)) {
                return;
            }

            if (currentValue) {
                input.value = currentValue + ', ' + tag;
            } else {
                input.value = tag;
            }
        }
    </script>
@endsection
