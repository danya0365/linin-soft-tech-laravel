{{--
    Image Crop Upload Component
    
    Usage:
    <x-image-crop-upload 
        name="photo"
        label="Photo"
        :current-image="$model->photo ?? null"
    />
    
    Props:
    - name: Input name attribute (required)
    - label: Label text (default: 'Upload Photo')
    - currentImage: Current image path for edit mode (optional)
    - ratios: Array of aspect ratios ['label' => ratio] (optional)
    - maxWidth: Max output width in px (default: 1920)
    - quality: JPEG quality 0-1 (default: 0.8)
    - required: Boolean for required field
--}}

@props([
    'name',
    'label' => 'Upload Photo',
    'currentImage' => null,
    'ratios' => null,
    'maxWidth' => 1920,
    'quality' => 0.8,
    'required' => false,
])

@php
    $uid = 'crop_' . $name . '_' . uniqid();
    $defaultRatios = [
        ['label' => 'Free', 'value' => 0],
        ['label' => '1:1', 'value' => 1],
        ['label' => '4:3', 'value' => 4/3],
        ['label' => '16:9', 'value' => 16/9],
        ['label' => '3:4', 'value' => 3/4],
    ];
    $ratioList = $ratios ?? $defaultRatios;
@endphp

<div class="mb-4" id="{{ $uid }}_wrapper" 
     data-max-width="{{ $maxWidth }}" 
     data-quality="{{ $quality }}">
    
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    {{-- Current Image Preview (edit mode) --}}
    @if($currentImage)
        <div class="mb-3" id="{{ $uid }}_current">
            <img src="{{ asset($currentImage) }}" alt="Current Photo" class="h-32 w-auto object-cover rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Current photo</p>
        </div>
    @endif

    {{-- Cropped Preview --}}
    <div class="mb-3 hidden" id="{{ $uid }}_preview_wrapper">
        <img id="{{ $uid }}_preview" src="" alt="Cropped Preview" class="h-40 w-auto object-cover rounded-lg border-2 border-green-400 dark:border-green-500 shadow-md">
        <div class="flex items-center gap-2 mt-2">
            <span class="text-xs text-green-600 dark:text-green-400 font-medium">
                <i class="fa fa-check-circle"></i> Cropped
            </span>
            <span class="text-xs text-gray-500 dark:text-gray-400" id="{{ $uid }}_size_info"></span>
            <button type="button" 
                    class="text-xs text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 underline ml-2"
                    onclick="window.ImageCropUpload.remove('{{ $uid }}')">
                <i class="fa fa-times"></i> Remove
            </button>
        </div>
    </div>

    {{-- File Input Trigger --}}
    <div id="{{ $uid }}_input_area">
        <input 
            type="file" 
            id="{{ $uid }}_file_input"
            accept="image/*"
            class="w-full px-4 py-2.5 border rounded-lg transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/50 dark:file:text-indigo-300 {{ $errors->has($name) ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }} focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400"
            {{ $required ? 'required' : '' }}
        >
    </div>

    {{-- Hidden input to hold the cropped file --}}
    <input type="file" name="{{ $name }}" id="{{ $uid }}_hidden_input" class="hidden">

    @error($name)
        <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
            <i class="fa fa-exclamation-circle"></i>
            <span>{{ $message }}</span>
        </p>
    @enderror

    {{-- Crop Modal --}}
    <div id="{{ $uid }}_modal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" 
             onclick="window.ImageCropUpload.closeModal('{{ $uid }}')"></div>
        
        {{-- Modal Content --}}
        <div class="fixed inset-4 sm:inset-8 md:inset-12 lg:inset-16 flex items-center justify-center">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-3xl max-h-full flex flex-col overflow-hidden">
                
                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fa fa-crop-simple text-indigo-500"></i>
                        Crop Image
                    </h3>
                    <button type="button" 
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                            onclick="window.ImageCropUpload.closeModal('{{ $uid }}')">
                        <i class="fa fa-times text-xl"></i>
                    </button>
                </div>

                {{-- Ratio Buttons --}}
                <div class="px-6 py-3 border-b border-gray-100 dark:border-gray-700 flex flex-wrap items-center gap-2">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 mr-1">Ratio:</span>
                    @foreach($ratioList as $index => $ratio)
                        <button type="button"
                                class="crop-ratio-btn px-3 py-1.5 text-xs font-medium rounded-full border transition-all duration-200
                                       {{ $index === 0 ? 'bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 border-indigo-300 dark:border-indigo-600' : 'bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-200 dark:hover:border-indigo-700' }}"
                                data-ratio="{{ $ratio['value'] }}"
                                onclick="window.ImageCropUpload.setRatio('{{ $uid }}', {{ $ratio['value'] }}, this)">
                            {{ $ratio['label'] }}
                        </button>
                    @endforeach
                </div>

                {{-- Toolbar --}}
                <div class="px-6 py-2 border-b border-gray-100 dark:border-gray-700 flex flex-wrap items-center gap-1">
                    <button type="button" class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200 transition-colors" title="Zoom In"
                            onclick="window.ImageCropUpload.zoom('{{ $uid }}', 0.1)">
                        <i class="fa fa-search-plus"></i>
                    </button>
                    <button type="button" class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200 transition-colors" title="Zoom Out"
                            onclick="window.ImageCropUpload.zoom('{{ $uid }}', -0.1)">
                        <i class="fa fa-search-minus"></i>
                    </button>
                    <div class="w-px h-6 bg-gray-200 dark:bg-gray-600 mx-1"></div>
                    <button type="button" class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200 transition-colors" title="Rotate Left"
                            onclick="window.ImageCropUpload.rotate('{{ $uid }}', -90)">
                        <i class="fa fa-rotate-left"></i>
                    </button>
                    <button type="button" class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200 transition-colors" title="Rotate Right"
                            onclick="window.ImageCropUpload.rotate('{{ $uid }}', 90)">
                        <i class="fa fa-rotate-right"></i>
                    </button>
                    <div class="w-px h-6 bg-gray-200 dark:bg-gray-600 mx-1"></div>
                    <button type="button" class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200 transition-colors" title="Flip Horizontal"
                            onclick="window.ImageCropUpload.flip('{{ $uid }}', 'x')">
                        <i class="fa fa-arrows-left-right"></i>
                    </button>
                    <button type="button" class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200 transition-colors" title="Flip Vertical"
                            onclick="window.ImageCropUpload.flip('{{ $uid }}', 'y')">
                        <i class="fa fa-arrows-up-down"></i>
                    </button>
                    <div class="w-px h-6 bg-gray-200 dark:bg-gray-600 mx-1"></div>
                    <button type="button" class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200 transition-colors" title="Reset"
                            onclick="window.ImageCropUpload.reset('{{ $uid }}')">
                        <i class="fa fa-undo"></i>
                    </button>
                </div>

                {{-- Cropper Area --}}
                <div class="flex-1 overflow-hidden px-4 py-4 bg-gray-100 dark:bg-gray-900/50" style="min-height: 300px; max-height: 60vh;">
                    <img id="{{ $uid }}_crop_image" src="" alt="Crop" class="block max-w-full" style="display: none;">
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
                            onclick="window.ImageCropUpload.closeModal('{{ $uid }}')">
                        Cancel
                    </button>
                    <button type="button" 
                            class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition-colors flex items-center gap-2"
                            onclick="window.ImageCropUpload.confirm('{{ $uid }}')">
                        <i class="fa fa-check"></i>
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
