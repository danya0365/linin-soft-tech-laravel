# 🎨 Dark Mode & Reusable Components Guide

## ✨ ภาพรวม

ระบบนี้ใช้ **Tailwind CSS Dark Mode** แบบ `class` strategy พร้อมกับ **Reusable Blade Components** สำหรับการพัฒนาที่มีประสิทธิภาพ

---

## 🌙 Dark Mode

### การทำงาน

Dark mode ใช้ 3 โหมด:
1. **Light** - โหมดสว่าง
2. **Dark** - โหมดมืด
3. **System** - ตามระบบ OS

### การใช้งาน

#### 1. Toggle Button
ใส่ในส่วน navigation:
```blade
<x-theme-toggle />
```

#### 2. ใช้ Dark Mode Classes
```html
<!-- Background -->
<div class="bg-white dark:bg-gray-900">

<!-- Text -->
<p class="text-gray-900 dark:text-white">

<!-- Border -->
<div class="border-gray-200 dark:border-gray-700">

<!-- Hover -->
<button class="hover:bg-gray-100 dark:hover:bg-gray-800">
```

#### 3. Custom Dark Colors (จาก tailwind.config.js)
```html
<div class="bg-dark-bg">        <!-- #0f172a -->
<div class="bg-dark-card">      <!-- #1e293b -->
<div class="border-dark-border"> <!-- #334155 -->
<p class="text-dark-text">      <!-- #e2e8f0 -->
```

---

## 🧩 Reusable Components

### 1. Button Component

**Location:** `resources/views/components/ui/button.blade.php`

#### Basic Usage
```blade
<!-- Primary button -->
<x-ui.button>Click Me</x-ui.button>

<!-- Different variants -->
<x-ui.button variant="secondary">Secondary</x-ui.button>
<x-ui.button variant="success">Success</x-ui.button>
<x-ui.button variant="danger">Delete</x-ui.button>
<x-ui.button variant="ghost">Ghost</x-ui.button>

<!-- Sizes -->
<x-ui.button size="sm">Small</x-ui.button>
<x-ui.button size="md">Medium</x-ui.button>
<x-ui.button size="lg">Large</x-ui.button>

<!-- With Icon -->
<x-ui.button>
    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
    </svg>
    Add New
</x-ui.button>

<!-- Icon Only -->
<x-ui.button icon-only :icon="$iconSvg" />

<!-- As Link -->
<x-ui.button href="{{ route('dashboard') }}">Go to Dashboard</x-ui.button>

<!-- Form Submit -->
<x-ui.button type="submit">Submit Form</x-ui.button>
```

#### Props
- `variant`: primary, secondary, success, danger, ghost (default: primary)
- `size`: sm, md, lg (default: md)
- `type`: button, submit, reset (default: button)
- `href`: URL สำหรับทำเป็น link
- `icon`: SVG icon string
- `iconOnly`: boolean สำหรับแสดงแค่ icon

---

### 2. Card Component

**Location:** `resources/views/components/ui/card.blade.php`

#### Basic Usage
```blade
<!-- Default card -->
<x-ui.card>
    <h3 class="text-lg font-semibold">Card Title</h3>
    <p>Card content here...</p>
</x-ui.card>

<!-- Glass effect -->
<x-ui.card variant="glass">
    Modern glassmorphism card
</x-ui.card>

<!-- With border -->
<x-ui.card variant="bordered">
    Bordered card
</x-ui.card>

<!-- Different padding -->
<x-ui.card padding="none">No padding</x-ui.card>
<x-ui.card padding="sm">Small padding</x-ui.card>
<x-ui.card padding="lg">Large padding</x-ui.card>

<!-- Custom classes -->
<x-ui.card class="hover:shadow-2xl">
    Hover effect card
</x-ui.card>
```

#### Props
- `variant`: default, glass, bordered (default: default)
- `padding`: none, sm, md, lg (default: md)

---

### 3. Input Component

**Location:** `resources/views/components/ui/input.blade.php`

#### Basic Usage
```blade
<!-- Simple input -->
<x-ui.input 
    name="email" 
    type="email" 
    label="Email Address"
    placeholder="you@example.com"
/>

<!-- With icon -->
<x-ui.input 
    name="search" 
    label="Search"
    :icon="'<svg>...</svg>'"
/>

<!-- Required field -->
<x-ui.input 
    name="username" 
    label="Username"
    required
/>

<!-- With error -->
<x-ui.input 
    name="password" 
    type="password"
    label="Password"
    error="Password is required"
/>

<!-- With helper text -->
<x-ui.input 
    name="bio" 
    label="Bio"
    helper="Tell us about yourself (max 200 characters)"
/>

<!-- Full example -->
<form>
    <x-ui.input 
        name="email" 
        type="email"
        label="Email Address"
        placeholder="you@example.com"
        required
        helper="We'll never share your email"
        :error="$errors->first('email')"
    />
</form>
```

#### Props
- `label`: Label text
- `error`: Error message
- `helper`: Helper text
- `icon`: SVG icon string
- `type`: input type (default: text)
- `required`: boolean

---

## 🎨 Color Palette

### Light Mode
```css
- Background: slate-50, blue-50, indigo-50
- Text: gray-900, gray-700
- Cards: white
- Borders: gray-200
- Hover: indigo-50, purple-50
```

### Dark Mode
```css
- Background: gray-900, gray-800
- Text: white, gray-200
- Cards: dark-card (#1e293b)
- Borders: dark-border (#334155)
- Hover: gray-700, gray-800
```

### Brand Colors
```css
- Primary: indigo-600, purple-600
- Success: green-600
- Danger: red-600
- Warning: yellow-500
```

---

## 📁 โครงสร้างไฟล์

```
resources/views/
├── components/
│   ├── theme-toggle.blade.php  # Dark mode toggle
│   └── ui/
│       ├── button.blade.php    # Reusable button
│       ├── card.blade.php      # Reusable card
│       └── input.blade.php     # Reusable input
├── layouts/
│   ├── app.blade.php          # Main layout (with dark mode init)
│   └── ...
└── auth/
    └── login.blade.php        # Login page
```

---

## 🚀 ตัวอย่างการใช้งานจริง

### Login Form with Components

```blade
<x-ui.card variant="glass" padding="lg">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
        Sign In
    </h2>
    
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        
        <x-ui.input 
            name="email" 
            type="email"
            label="Email Address"
            placeholder="your.email@example.com"
            :error="$errors->first('email')"
            required
        />
        
        <x-ui.input 
            name="password" 
            type="password"
            label="Password"
            placeholder="••••••••"
            :error="$errors->first('password')"
            required
        />
        
        <div class="flex items-center justify-between">
            <label class="flex items-center">
                <input type="checkbox" name="remember" class="rounded">
                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                    Remember me
                </span>
            </label>
            
            <a href="{{ route('password.request') }}" 
               class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                Forgot password?
            </a>
        </div>
        
        <x-ui.button type="submit" class="w-full">
            Login
        </x-ui.button>
    </form>
</x-ui.card>
```

### Data Table Card

```blade
<x-ui.card>
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            Users
        </h3>
        <x-ui.button href="{{ route('users.create') }}" size="sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add User
        </x-ui.button>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <!-- Table content -->
        </table>
    </div>
</x-ui.card>
```

---

## 💡 Best Practices

1. **ใช้ Components แทน HTML ธรรมดา**
   - ✅ `<x-ui.button>Click</x-ui.button>`
   - ❌ `<button class="...">Click</button>`

2. **ใส่ Dark Mode Classes ทุกครั้ง**
   ```html
   <!-- ✅ Good -->
   <div class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
   
   <!-- ❌ Bad -->
   <div class="bg-white text-gray-900">
   ```

3. **ใช้ Brand Colors**
   ```html
   <!-- ✅ Good -->
   <button class="bg-primary-600 dark:bg-primary-500">
   
   <!-- ❌ Bad -->
   <button class="bg-blue-600">
   ```

4. **Transition Smooth**
   ```html
   <div class="transition-colors duration-200">
   ```

---

## 🔧 Customization

### เพิ่ม Color ใหม่
แก้ `tailwind.config.js`:
```javascript
theme: {
    extend: {
        colors: {
            'custom': {
                500: '#yourcolor',
            }
        }
    }
}
```

### สร้าง Component ใหม่
```bash
# Create new component
touch resources/views/components/ui/badge.blade.php
```

### Rebuild Tailwind
```bash
npm run dev
# or
npm run production
```

---

**Created:** 2026-01-18  
**Status:** ✅ Ready to use  
**Version:** 1.0.0
