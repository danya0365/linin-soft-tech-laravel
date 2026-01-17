# Bootstrap to Tailwind Migration Plan

## วัตถุประสงค์
Redesign และ Migrate ระบบ LinenSoftTech ให้ใช้ Tailwind CSS เท่านั้น เพื่อ:
- ✅ แก้ปัญหาการ conflict ระหว่าง CSS frameworks
- ✅ ลดขนาด CSS bundle
- ✅ เพิ่มความยืดหยุ่นในการออกแบบ
- ✅ ใช้ modern CSS practices

---

## Phase 1: การวิเคราะห์และวางแผน (1-2 วัน)

### 1.1 สำรวจการใช้งาน Bootstrap
```bash
# หา Bootstrap classes ที่ใช้ในโปรเจค
grep -r "class=" resources/views --include="*.blade.php" | grep -E "(btn|card|modal|nav|dropdown|alert|badge|form-control|table|container|row|col-)"
```

**เป้าหมาย:**
- [ ] List ทุก Bootstrap components ที่ใช้
- [ ] นับจำนวนไฟล์ที่ต้องแก้
- [ ] ระบุ components ที่ซับซ้อน (Modal, Dropdown, etc.)

### 1.2 สร้าง Component Mapping Table

| Bootstrap Component | Tailwind Equivalent | Priority | Notes |
|-------------------|-------------------|----------|-------|
| `.btn` | Custom button classes | High | ใช้บ่อยที่สุด |
| `.card` | `bg-white rounded-lg shadow` | High | ใช้ทั่วทั้งระบบ |
| `.modal` | Custom modal component | Medium | ใช้ Alpine.js หรือ Modal library |
| `.form-control` | `border rounded px-3 py-2` | High | Input fields |
| `.table` | Custom table classes | Medium | |
| `.nav`, `.navbar` | Custom nav classes | High | Navigation |
| `.dropdown` | Alpine.js dropdown | Medium | |
| `.alert` | Custom alert component | Low | |
| `.badge` | Custom badge classes | Low | |
| Grid (`.row`, `.col-*`) | Tailwind Grid/Flex | High | Layout system |

---

## Phase 2: การเตรียมความพร้อม (1 วัน)

### 2.1 Setup Tailwind Config
**ไฟล์:** `tailwind.config.js`

```javascript
module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        // Brand colors
        primary: {
          50: '#f0f9ff',
          100: '#e0f2fe',
          // ... สีของแบรนด์
        },
        // Add custom colors if needed
      },
      fontFamily: {
        sans: ['Nunito', 'sans-serif'],
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}
```

### 2.2 สร้าง Tailwind Components
**ไฟล์:** `resources/css/components.css`

```css
@layer components {
  /* Buttons */
  .btn {
    @apply px-4 py-2 rounded-lg font-semibold transition-all duration-200;
  }
  
  .btn-primary {
    @apply bg-gradient-to-r from-indigo-600 to-purple-600 text-white hover:from-indigo-700 hover:to-purple-700 shadow-lg hover:shadow-xl;
  }
  
  .btn-secondary {
    @apply bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600;
  }
  
  .btn-danger {
    @apply bg-red-600 text-white hover:bg-red-700 shadow-lg hover:shadow-xl;
  }
  
  /* Cards */
  .card {
    @apply bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 transition-all duration-200;
  }
  
  .card-body {
    @apply p-6;
  }
  
  .card-header {
    @apply px-6 py-4 border-b border-gray-200 dark:border-gray-700 font-semibold;
  }
  
  /* Forms */
  .form-input {
    @apply w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg 
           focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent
           bg-white dark:bg-gray-700 text-gray-900 dark:text-white
           placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200;
  }
  
  .form-label {
    @apply block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2;
  }
  
  /* Tables */
  .table {
    @apply w-full text-sm text-left;
  }
  
  .table-header {
    @apply bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600;
  }
  
  .table th {
    @apply px-6 py-3 text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider;
  }
  
  .table td {
    @apply px-6 py-4 text-gray-900 dark:text-gray-100;
  }
  
  .table-row {
    @apply border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150;
  }
}
```

### 2.3 Install Dependencies
```bash
npm install -D @tailwindcss/forms @tailwindcss/typography
```

---

## Phase 3: Migration แบบค่อยเป็นค่อยไป (2-4 สัปดาห์)

### 3.1 ลำดับความสำคัญในการ Migrate

**สัปดาห์ที่ 1: Authentication & Public Pages**
- [ ] Login page (`resources/views/auth/login.blade.php`) ✅ Done!
- [ ] Register page
- [ ] Forgot password page
- [ ] Landing page (ถ้ามี)

**สัปดาห์ที่ 2: Core Layouts & Components**
- [ ] Layouts: `layouts/app.blade.php`, `layouts/admin.blade.php`, `layouts/manager.blade.php`
- [ ] Navbar components
- [ ] Sidebar components
- [ ] Footer components

**สัปดาห์ที่ 3: Data Tables & Forms**
- [ ] All index pages (tables)
- [ ] All create/edit forms
- [ ] Search & filter components

**สัปดาห์ที่ 4: Advanced Features**
- [ ] Modals
- [ ] Dropdowns
- [ ] Alerts/Notifications
- [ ] Charts (ถ้ามี)

### 3.2 Migration Checklist สำหรับแต่ละไฟล์

```markdown
## ไฟล์: [ชื่อไฟล์]

### Before Migration
- [ ] Backup ไฟล์เดิม
- [ ] Screenshot หน้าจอเดิม (light + dark mode)
- [ ] Test functionality เดิม

### During Migration
- [ ] แทนที่ Bootstrap classes ด้วย Tailwind
- [ ] ใช้ custom components ที่สร้างไว้
- [ ] ตรวจสอบ responsive design
- [ ] ทดสอบ dark mode

### After Migration
- [ ] Screenshot หน้าจอใหม่ (light + dark mode)
- [ ] Compare กับเดิม
- [ ] Test ทุก functionality
- [ ] Test บน browser ต่างๆ
- [ ] Get feedback จากทีม
```

---

## Phase 4: ลบ Bootstrap (1 วัน)

### 4.1 เมื่อ Migrate ครบทุกหน้าแล้ว

**แก้ไขไฟล์:** `resources/sass/app.scss`

```scss
@tailwind base;
@tailwind components;
@tailwind utilities;

// Fonts
@import url('https://fonts.googleapis.com/css?family=Nunito');

// Variables (ถ้ายังต้องใช้)
// @import 'variables';

// ❌ ลบ Bootstrap
// @import '~bootstrap/scss/bootstrap';

// Icons - เก็บไว้ถ้ายังใช้
@import '~bootstrap-icons/font/bootstrap-icons';
@import '~@fortawesome/fontawesome-free/css/fontawesome';
@import '~@fortawesome/fontawesome-free/css/regular';
@import '~@fortawesome/fontawesome-free/css/solid';
@import '~@fortawesome/fontawesome-free/css/brands';
@import 'css.gg/icons/all.css';

// Custom styles
@import 'components'; // Tailwind components ใหม่
@import 'customs';
```

### 4.2 Uninstall Bootstrap
```bash
npm uninstall bootstrap bootstrap-icons
npm uninstall @popperjs/core # ถ้าไม่ได้ใช้ที่อื่น
```

### 4.3 Clean up
```bash
# Rebuild CSS
npm run dev

# ลบไฟล์ที่ไม่ใช้แล้ว
rm resources/sass/_variables.scss # ถ้าเป็น Bootstrap variables

# Test ทุกหน้า
```

---

## Phase 5: Optimization (1 วัน)

### 5.1 Performance Optimization
- [ ] ใช้ Tailwind's purge/content config ให้ถูกต้อง
- [ ] Remove unused CSS
- [ ] Optimize images
- [ ] Setup CSS minification

### 5.2 Documentation
- [ ] Document custom components
- [ ] Update style guide
- [ ] Train team members

---

## Tools & Resources

### Migration Tools
1. **Windy** - Bootstrap to Tailwind converter (ใช้เป็น reference)
   - https://usewindy.com/

2. **Regex Patterns** - สำหรับ find & replace
   ```regex
   # หา Bootstrap buttons
   class="[^"]*\bbtn\b[^"]*"
   
   # หา Bootstrap grid
   class="[^"]*\b(row|col-)\b[^"]*"
   ```

### Testing Checklist
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Mobile Chrome
- [ ] Mobile Safari
- [ ] Tablet view
- [ ] Dark mode ทุก browser

---

## Estimated Timeline

| Phase | Duration | Status |
|-------|----------|--------|
| Phase 1: Analysis | 1-2 days | 🔜 |
| Phase 2: Setup | 1 day | 🔜 |
| Phase 3: Migration | 2-4 weeks | 🔜 |
| Phase 4: Remove Bootstrap | 1 day | 🔜 |
| Phase 5: Optimization | 1 day | 🔜 |
| **Total** | **3-5 weeks** | |

---

## Notes & Tips

### ⚠️ Common Pitfalls
1. **อย่า migrate ทุกอย่างพร้อมกัน** - ทำทีละหน้า/component
2. **Test บ่อยๆ** - อย่ารอจนเสร็จหมดค่อย test
3. **Backup เสมอ** - ใช้ Git branching strategy
4. **Dark mode** - ต้อง test ทุกหน้าหลังแก้

### 💡 Best Practices
1. ใช้ Git branches: `feature/tailwind-migration-auth`, `feature/tailwind-migration-tables`
2. Create reusable components
3. Document เมื่อเจอปัญหา
4. Keep design consistent

---

## Help & Support

- **Tailwind Docs:** https://tailwindcss.com/docs
- **Tailwind UI:** https://tailwindui.com/ (มี free components)
- **Headless UI:** https://headlessui.com/ (สำหรับ modals, dropdowns)
- **Alpine.js:** https://alpinejs.dev/ (JavaScript framework ที่เบา สำหรับ interactivity)
