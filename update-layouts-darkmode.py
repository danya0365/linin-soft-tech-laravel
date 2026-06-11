#!/usr/bin/env python3
"""
Auto-update remaining layouts with dark mode support
"""

import re
import os

LAYOUTS = [
    "resources/views/layouts/admin.blade.php",
    "resources/views/layouts/supervisor.blade.php",
    "resources/views/layouts/worker.blade.php",
    "resources/views/layouts/user-customer.blade.php",
]

def update_layout(filepath):
    if not os.path.exists(filepath):
        print(f"⚠️  Not found: {filepath}")
        return False
    
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # 1. Add dark mode init script
    if "Dark Mode Initialization" not in content:
        content = content.replace(
            '    </style>\n\n    <!-- Scripts -->',
            '''    </style>

    <!-- Dark Mode Initialization (prevent flash) -->
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'system';
            if (theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <!-- Scripts -->'''
        )
    
    # 2. Update body tag
    content = re.sub(
        r'<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">',
        '<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 min-h-screen transition-colors duration-200">',
        content
    )
    
    # 3. Update main tag
    content = re.sub(
        r'<main class="flex-grow py-8 px-4 sm:px-6 lg:px-8">',
        '<main class="flex-grow py-8 px-4 sm:px-6 lg:px-8 transition-colors duration-200">',
        content
    )
    
    # 4. Update footer tag
    content = re.sub(
        r'<footer class="bg-white/80 backdrop-blur-md border-t border-gray-200 mt-auto">',
        '<footer class="bg-white/80 dark:bg-dark-card/80 backdrop-blur-md border-t border-gray-200 dark:border-dark-border mt-auto transition-colors duration-200">',
        content
    )
    
    # 5. Update footer text
    content = re.sub(
        r'<p class="text-sm text-gray-600">',
        '<p class="text-sm text-gray-600 dark:text-gray-300">',
        content
    )
    
    # 6. Update footer div
    content = re.sub(
        r'<div class="flex items-center gap-4 text-sm text-gray-500">',
        '<div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">',
        content
    )
    
    # 7. Update footer link
    content = re.sub(
        r'class="hover:text-indigo-600 transition-colors duration-200 flex items-center gap-1">',
        'class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-200 flex items-center gap-1">',
        content
    )
    
    # Backup
    with open(filepath + '.bak5', 'w', encoding='utf-8') as f:
        f.write(content)
    
    # Write
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)
    
    print(f"✅ Updated: {filepath}")
    return True

def main():
    print("🔧 Updating layouts with dark mode support...")
    print()
    
    updated = 0
    for filepath in LAYOUTS:
        if update_layout(filepath):
            updated += 1
    
    print()
    print(f"📊 Updated: {updated}/{len(LAYOUTS)} layouts")
    print("✅ Done!")

if __name__ == "__main__":
    main()
