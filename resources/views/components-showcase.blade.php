@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <h1 class="text-3xl font-bold mb-8 text-gray-900 dark:text-white">UI Components Showcase</h1>
    
    {{-- Cards Section --}}
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Cards</h2>
        
        <div class="grid md:grid-cols-2 gap-6">
            {{-- Basic Card --}}
            <x-ui.card>
                <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">Basic Card</h3>
                <p class="text-gray-600 dark:text-gray-400">This is a simple card with no header or footer.</p>
            </x-ui.card>
            
            {{-- Card with Header --}}
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Card with Header</h3>
                </x-slot:header>
                <p class="text-gray-600 dark:text-gray-400">This card has a header section.</p>
            </x-ui.card>
            
            {{-- Card with Header and Footer --}}
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Full Card</h3>
                </x-slot:header>
                <p class="text-gray-600 dark:text-gray-400">This card has both header and footer.</p>
                <x-slot:footer>
                    <div class="flex gap-2">
                        <x-ui.button variant="primary" size="sm">Action</x-ui.button>
                        <x-ui.button variant="secondary" size="sm">Cancel</x-ui.button>
                    </div>
                </x-slot:footer>
            </x-ui.card>
            
            {{-- Hoverable Card --}}
            <x-ui.card hover="true">
                <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">Hoverable Card</h3>
                <p class="text-gray-600 dark:text-gray-400">Hover over this card to see the effect!</p>
            </x-ui.card>
        </div>
    </section>
    
    {{-- Buttons Section --}}
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Buttons</h2>
        
        <x-ui.card>
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Button Variants</h3>
            <div class="flex flex-wrap gap-3 mb-6">
                <x-ui.button variant="primary">Primary</x-ui.button>
                <x-ui.button variant="secondary">Secondary</x-ui.button>
                <x-ui.button variant="success">Success</x-ui.button>
                <x-ui.button variant="danger">Danger</x-ui.button>
                <x-ui.button variant="warning">Warning</x-ui.button>
                <x-ui.button variant="info">Info</x-ui.button>
                <x-ui.button variant="outline">Outline</x-ui.button>
            </div>
            
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Button Sizes</h3>
            <div class="flex flex-wrap items-center gap-3 mb-6">
                <x-ui.button size="sm">Small</x-ui.button>
                <x-ui.button size="md">Medium</x-ui.button>
                <x-ui.button size="lg">Large</x-ui.button>
            </div>
            
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Buttons with Icons</h3>
            <div class="flex flex-wrap gap-3 mb-6">
                <x-ui.button icon="fa fa-plus">Create New</x-ui.button>
                <x-ui.button variant="success" icon="fa fa-check">Save</x-ui.button>
                <x-ui.button variant="danger" icon="fa fa-trash">Delete</x-ui.button>
                <x-ui.button variant="info" icon="fa fa-eye">View</x-ui.button>
                <x-ui.button variant="secondary" icon="fa fa-edit">Edit</x-ui.button>
            </div>
            
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Button States</h3>
            <div class="flex flex-wrap gap-3">
                <x-ui.button>Normal</x-ui.button>
                <x-ui.button disabled>Disabled</x-ui.button>
                <x-ui.button href="{{ route('admin') }}">As Link</x-ui.button>
            </div>
        </x-ui.card>
    </section>
    
    {{-- Badges Section --}}
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Badges</h2>
        
        <x-ui.card>
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Badge Variants</h3>
            <div class="flex flex-wrap gap-3 mb-6">
                <x-ui.badge variant="primary">Primary</x-ui.badge>
                <x-ui.badge variant="secondary">Secondary</x-ui.badge>
                <x-ui.badge variant="success">Success</x-ui.badge>
                <x-ui.badge variant="danger">Danger</x-ui.badge>
                <x-ui.badge variant="warning">Warning</x-ui.badge>
                <x-ui.badge variant="info">Info</x-ui.badge>
            </div>
            
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Badge Sizes</h3>
            <div class="flex flex-wrap items-center gap-3 mb-6">
                <x-ui.badge size="sm">Small</x-ui.badge>
                <x-ui.badge size="md">Medium</x-ui.badge>
                <x-ui.badge size="lg">Large</x-ui.badge>
            </div>
            
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Badge Shapes</h3>
            <div class="flex flex-wrap gap-3">
                <x-ui.badge rounded="true">Pill Badge</x-ui.badge>
                <x-ui.badge rounded="false">Square Badge</x-ui.badge>
            </div>
        </x-ui.card>
    </section>
    
    {{-- Alerts Section --}}
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Alerts</h2>
        
        <div class="space-y-4">
            <x-ui.alert variant="success">
                <strong>Success!</strong> Your changes have been saved successfully.
            </x-ui.alert>
            
            <x-ui.alert variant="danger">
                <strong>Error!</strong> There was a problem processing your request.
            </x-ui.alert>
            
            <x-ui.alert variant="warning">
                <strong>Warning!</strong> This action cannot be undone.
            </x-ui.alert>
            
            <x-ui.alert variant="info">
                <strong>Info:</strong> You have 3 new notifications.
            </x-ui.alert>
            
            <x-ui.alert variant="success" dismissible="true">
                <strong>Dismissible Alert:</strong> Click the X to dismiss this alert.
            </x-ui.alert>
        </div>
    </section>
    
    {{-- Combined Example --}}
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Combined Example</h2>
        
        <x-ui.card hover="true">
            <x-slot:header>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">User Profile</h3>
                        <x-ui.badge variant="success">Active</x-ui.badge>
                    </div>
                    <x-ui.button size="sm" icon="fa fa-edit">Edit</x-ui.button>
                </div>
            </x-slot:header>
            
            <div class="space-y-4">
                <x-ui.alert variant="info">
                    Profile completion: 80%
                </x-ui.alert>
                
                <div class="grid md:grid-cols-2 gap-4 text-gray-900 dark:text-white">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Name</p>
                        <p class="font-semibold">John Doe</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                        <p class="font-semibold">john@example.com</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Role</p>
                        <x-ui.badge variant="primary">Administrator</x-ui.badge>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                        <x-ui.badge variant="success">Online</x-ui.badge>
                    </div>
                </div>
            </div>
            
            <x-slot:footer>
                <div class="flex gap-2">
                    <x-ui.button variant="primary">Save Changes</x-ui.button>
                    <x-ui.button variant="danger">Delete Account</x-ui.button>
                    <x-ui.button variant="secondary">Cancel</x-ui.button>
                </div>
            </x-slot:footer>
        </x-ui.card>
    </section>
</div>

{{-- Alpine.js for dismissible alerts --}}
@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush
@endsection
