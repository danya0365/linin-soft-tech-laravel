# Bootstrap to Tailwind Quick Reference

## 🎯 Bootstrap Classes → Tailwind Equivalents

### Layout & Grid

| Bootstrap | Tailwind | Notes |
|-----------|----------|-------|
| `.container` | `.container` (custom) or `.max-w-7xl .mx-auto .px-4` | |
| `.container-fluid` | `.w-full .px-4` | |
| `.row` | `.flex .flex-wrap .-mx-4` | ใช้ negative margin |
| `.col` | `.flex-1 .px-4` | |
| `.col-6` | `.w-1/2 .px-4` | |
| `.col-md-6` | `.md:w-1/2 .px-4` | |
| `.col-lg-4` | `.lg:w-1/3 .px-4` | |
| `.d-none` | `.hidden` | |
| `.d-block` | `.block` | |
| `.d-flex` | `.flex` | |
| `.d-inline-flex` | `.inline-flex` | |
| `.d-grid` | `.grid` | |

### Spacing

| Bootstrap | Tailwind | Example |
|-----------|----------|---------|
| `.m-0` | `.m-0` | No margin |
| `.m-1` | `.m-1` (4px) | |
| `.m-2` | `.m-2` (8px) | |
| `.m-3` | `.m-3` (12px) | |
| `.m-4` | `.m-4` (16px) | |
| `.m-5` | `.m-6` (24px) | ⚠️ Different scale |
| `.mt-3` | `.mt-3` | Margin top |
| `.mb-4` | `.mb-4` | Margin bottom |
| `.mx-auto` | `.mx-auto` | Center horizontally |
| `.p-3` | `.p-3` | Padding |
| `.px-4` | `.px-4` | Padding x-axis |
| `.py-2` | `.py-2` | Padding y-axis |

### Text & Typography

| Bootstrap | Tailwind |
|-----------|----------|
| `.text-left` | `.text-left` |
| `.text-center` | `.text-center` |
| `.text-right` | `.text-right` |
| `.text-uppercase` | `.uppercase` |
| `.text-lowercase` | `.lowercase` |
| `.text-capitalize` | `.capitalize` |
| `.font-weight-bold` | `.font-bold` |
| `.font-weight-normal` | `.font-normal` |
| `.font-italic` | `.italic` |
| `.text-muted` | `.text-gray-500 .dark:text-gray-400` |
| `.text-primary` | `.text-indigo-600` |
| `.text-success` | `.text-green-600` |
| `.text-danger` | `.text-red-600` |
| `.text-warning` | `.text-yellow-600` |
| `.text-info` | `.text-cyan-600` |
| `.h1` | `.text-4xl .font-bold` |
| `.h2` | `.text-3xl .font-bold` |
| `.h3` | `.text-2xl .font-bold` |
| `.h4` | `.text-xl .font-bold` |
| `.h5` | `.text-lg .font-bold` |
| `.h6` | `.text-base .font-bold` |

### Colors

| Bootstrap | Tailwind (Light Mode) | Tailwind (Dark Mode) |
|-----------|----------------------|---------------------|
| `.text-primary` | `.text-indigo-600` | `.dark:text-indigo-400` |
| `.text-secondary` | `.text-gray-600` | `.dark:text-gray-400` |
| `.text-success` | `.text-green-600` | `.dark:text-green-400` |
| `.text-danger` | `.text-red-600` | `.dark:text-red-400` |
| `.text-warning` | `.text-yellow-600` | `.dark:text-yellow-400` |
| `.text-info` | `.text-cyan-600` | `.dark:text-cyan-400` |
| `.bg-primary` | `.bg-indigo-600` | `.dark:bg-indigo-500` |
| `.bg-success` | `.bg-green-600` | `.dark:bg-green-500` |
| `.bg-danger` | `.bg-red-600` | `.dark:bg-red-500` |
| `.bg-light` | `.bg-gray-100` | `.dark:bg-gray-700` |
| `.bg-dark` | `.bg-gray-800` | `.dark:bg-gray-900` |

### Buttons

```html
<!-- Bootstrap -->
<button class="btn btn-primary">Click Me</button>
<button class="btn btn-secondary btn-sm">Small</button>
<button class="btn btn-outline-primary btn-lg">Outline Large</button>

<!-- Tailwind (using custom components) -->
<button class="btn btn-primary">Click Me</button>
<button class="btn btn-secondary btn-sm">Small</button>
<button class="btn btn-outline-primary btn-lg">Outline Large</button>

<!-- Tailwind (pure utility classes) -->
<button class="px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
  Click Me
</button>
```

### Cards

```html
<!-- Bootstrap -->
<div class="card">
  <div class="card-header">Header</div>
  <div class="card-body">
    <h5 class="card-title">Title</h5>
    <p class="card-text">Content</p>
  </div>
  <div class="card-footer">Footer</div>
</div>

<!-- Tailwind (using custom components) -->
<div class="card">
  <div class="card-header">Header</div>
  <div class="card-body">
    <h5 class="text-xl font-bold mb-2">Title</h5>
    <p class="text-gray-600 dark:text-gray-400">Content</p>
  </div>
  <div class="card-footer">Footer</div>
</div>

<!-- Tailwind (pure utility classes) -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
  <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 font-semibold">
    Header
  </div>
  <div class="p-6">
    <h5 class="text-xl font-bold mb-2 text-gray-900 dark:text-white">Title</h5>
    <p class="text-gray-600 dark:text-gray-400">Content</p>
  </div>
  <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
    Footer
  </div>
</div>
```

### Forms

```html
<!-- Bootstrap -->
<div class="form-group">
  <label for="email" class="form-label">Email</label>
  <input type="email" class="form-control" id="email" placeholder="Email">
  <small class="form-text text-muted">Help text</small>
</div>

<!-- Tailwind (using custom components) -->
<div class="form-group">
  <label for="email" class="form-label">Email</label>
  <input type="email" class="form-input" id="email" placeholder="Email">
  <small class="form-help">Help text</small>
</div>

<!-- Tailwind (pure utility classes) -->
<div class="mb-4">
  <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
    Email
  </label>
  <input 
    type="email" 
    id="email" 
    placeholder="Email"
    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg 
           bg-white dark:bg-gray-700 text-gray-900 dark:text-white 
           placeholder-gray-400 dark:placeholder-gray-500 
           focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent 
           transition-all duration-200"
  >
  <small class="mt-2 text-sm text-gray-500 dark:text-gray-400">Help text</small>
</div>
```

### Tables

```html
<!-- Bootstrap -->
<table class="table table-striped table-hover">
  <thead>
    <tr>
      <th>Name</th>
      <th>Email</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>John</td>
      <td>john@example.com</td>
    </tr>
  </tbody>
</table>

<!-- Tailwind (using custom components) -->
<div class="table-container">
  <table class="table table-striped table-hover">
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>John</td>
        <td>john@example.com</td>
      </tr>
    </tbody>
  </table>
</div>
```

### Alerts

```html
<!-- Bootstrap -->
<div class="alert alert-success" role="alert">
  Success message!
</div>

<!-- Tailwind (using custom component) -->
<div class="alert alert-success">
  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
  </svg>
  <span>Success message!</span>
</div>
```

### Badges

```html
<!-- Bootstrap -->
<span class="badge bg-primary">Primary</span>
<span class="badge bg-success">Success</span>

<!-- Tailwind (using custom component) -->
<span class="badge badge-primary">Primary</span>
<span class="badge badge-success">Success</span>
```

### Modals

```html
<!-- Bootstrap -->
<div class="modal fade" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modal title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Modal content
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save</button>
      </div>
    </div>
  </div>
</div>

<!-- Tailwind (with Alpine.js) -->
<div x-show="open" 
     x-cloak 
     class="modal-container">
  <div class="modal-backdrop" @click="open = false"></div>
  
  <div class="modal">
    <div class="modal-header">
      <h5 class="modal-title">Modal title</h5>
      <button @click="open = false" class="text-gray-400 hover:text-gray-600">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>
    
    <div class="modal-body">
      Modal content
    </div>
    
    <div class="modal-footer">
      <button @click="open = false" class="btn btn-secondary">Close</button>
      <button class="btn btn-primary">Save</button>
    </div>
  </div>
</div>
```

### Dropdowns

```html
<!-- Bootstrap -->
<div class="dropdown">
  <button class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
    Dropdown
  </button>
  <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#">Action</a></li>
    <li><a class="dropdown-item" href="#">Another action</a></li>
    <li><hr class="dropdown-divider"></li>
    <li><a class="dropdown-item" href="#">Something else</a></li>
  </ul>
</div>

<!-- Tailwind (with Alpine.js) -->
<div x-data="{ open: false }" class="dropdown">
  <button @click="open = !open" class="btn btn-secondary">
    Dropdown
    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
      <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
    </svg>
  </button>
  
  <div x-show="open" 
       @click.away="open = false"
       x-cloak
       class="dropdown-menu">
    <a href="#" class="dropdown-item">Action</a>
    <a href="#" class="dropdown-item">Another action</a>
    <div class="dropdown-divider"></div>
    <a href="#" class="dropdown-item">Something else</a>
  </div>
</div>
```

### Navigation

```html
<!-- Bootstrap -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Brand</a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link active" href="#">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">About</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Tailwind (with Alpine.js) -->
<nav class="bg-white dark:bg-gray-800 shadow">
  <div class="container">
    <div class="flex items-center justify-between h-16">
      <a href="#" class="text-xl font-bold text-gray-900 dark:text-white">Brand</a>
      
      <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
      
      <div class="hidden lg:flex items-center gap-6">
        <a href="#" class="text-indigo-600 dark:text-indigo-400 font-medium">Home</a>
        <a href="#" class="text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400">About</a>
      </div>
    </div>
  </div>
</nav>
```

---

## 💡 Tips & Best Practices

### 1. ใช้ Custom Components
แทนที่จะเขียน Tailwind classes ยาวๆ ซ้ำๆ ให้สร้าง custom components ใน `components.css`:

```css
@layer components {
  .btn-primary {
    @apply bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700;
  }
}
```

### 2. Dark Mode
เพิ่ม `dark:` variant สำหรับทุก component:

```html
<div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
  Content
</div>
```

### 3. Responsive Design
ใช้ responsive prefixes:

```html
<!-- Stack on mobile, row on desktop -->
<div class="flex flex-col lg:flex-row gap-4">
  <div class="w-full lg:w-1/2">Column 1</div>
  <div class="w-full lg:w-1/2">Column 2</div>
</div>
```

### 4. Interactive Components
ใช้ Alpine.js แทน Bootstrap JavaScript:

```bash
npm install alpinejs
```

```html
<div x-data="{ open: false }">
  <button @click="open = !open">Toggle</button>
  <div x-show="open">Content</div>
</div>
```

---

## 🔧 Find & Replace Patterns

### VS Code Regex สำหรับ Migration

```regex
# หา Bootstrap buttons
class="([^"]*)\bbtn\b([^"]*)"

# หา Bootstrap cards
class="([^"]*)\bcard\b([^"]*)"

# หา Bootstrap grid classes
class="([^"]*)\b(row|col-)\b([^"]*)"
```

---

## 📚 Resources

- **Tailwind CSS Docs**: https://tailwindcss.com/docs
- **Tailwind UI Components**: https://tailwindui.com/components (Free & Paid)
- **Headless UI**: https://headlessui.com/ (Unstyled accessible components)
- **Alpine.js**: https://alpinejs.dev/ (Lightweight JavaScript framework)
- **DaisyUI**: https://daisyui.com/ (Tailwind component library - alternative)
- **Flowbite**: https://flowbite.com/ (Tailwind components - alternative)
