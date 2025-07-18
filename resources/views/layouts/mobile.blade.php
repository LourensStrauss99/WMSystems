<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Workflow Management System') }}</title>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    @livewireStyles

    <!-- Theme System CSS -->
    <style>
        /* Light Theme (Default) */
        :root {
            --bg-primary: #ffffff;
            --bg-secondary: #f8f9fa;
            --bg-tertiary: #e9ecef;
            --text-primary: #212529;
            --text-secondary: #6c757d;
            --text-muted: #adb5bd;
            --border-color: #dee2e6;
            --shadow: rgba(0, 0, 0, 0.1);
            --shadow-hover: rgba(0, 0, 0, 0.15);
        }

        /* Dark Theme */
        [data-theme="dark"] {
            --bg-primary: #212529;
            --bg-secondary: #343a40;
            --bg-tertiary: #495057;
            --text-primary: #ffffff;
            --text-secondary: #adb5bd;
            --text-muted: #6c757d;
            --border-color: #495057;
            --shadow: rgba(0, 0, 0, 0.3);
            --shadow-hover: rgba(0, 0, 0, 0.4);
        }

        /* Apply theme variables */
        [data-theme="dark"] body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
        }

        [data-theme="dark"] .card {
            background-color: var(--bg-secondary);
            border-color: var(--border-color);
            color: var(--text-primary);
        }

        [data-theme="dark"] .card-header {
            background-color: var(--bg-tertiary) !important;
            border-color: var(--border-color);
            color: var(--text-primary);
        }

        [data-theme="dark"] .navbar {
            background-color: var(--bg-secondary) !important;
            border-color: var(--border-color);
        }

        [data-theme="dark"] .navbar .nav-link {
            color: var(--text-secondary) !important;
        }

        [data-theme="dark"] .navbar .nav-link:hover {
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .btn-outline-secondary {
            color: var(--text-secondary);
            border-color: var(--border-color);
        }

        [data-theme="dark"] .btn-outline-secondary:hover {
            background-color: var(--bg-tertiary);
            color: var(--text-primary);
        }

        [data-theme="dark"] .form-control {
            background-color: var(--bg-tertiary);
            border-color: var(--border-color);
            color: var(--text-primary);
        }

        [data-theme="dark"] .form-control:focus {
            background-color: var(--bg-tertiary);
            border-color: #0d6efd;
            color: var(--text-primary);
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        [data-theme="dark"] .form-control::placeholder {
            color: var(--text-muted);
        }

        [data-theme="dark"] .text-muted {
            color: var(--text-muted) !important;
        }

        [data-theme="dark"] .bg-light {
            background-color: var(--bg-tertiary) !important;
        }

        [data-theme="dark"] .border {
            border-color: var(--border-color) !important;
        }

        [data-theme="dark"] .dropdown-menu {
            background-color: var(--bg-secondary);
            border-color: var(--border-color);
        }

        [data-theme="dark"] .dropdown-item {
            color: var(--text-secondary);
        }

        [data-theme="dark"] .dropdown-item:hover {
            background-color: var(--bg-tertiary);
            color: var(--text-primary);
        }

        [data-theme="dark"] .alert-info {
            background-color: rgba(13, 202, 240, 0.1);
            border-color: rgba(13, 202, 240, 0.2);
            color: #0dcaf0;
        }

        [data-theme="dark"] .alert-success {
            background-color: rgba(25, 135, 84, 0.1);
            border-color: rgba(25, 135, 84, 0.2);
            color: #198754;
        }

        [data-theme="dark"] .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            border-color: rgba(220, 53, 69, 0.2);
            color: #dc3545;
        }

        [data-theme="dark"] .alert-warning {
            background-color: rgba(255, 193, 7, 0.1);
            border-color: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }

        /* Select2 Dark Theme */
        [data-theme="dark"] .select2-container--default .select2-selection--single {
            background-color: var(--bg-tertiary);
            border-color: var(--border-color);
            color: var(--text-primary);
        }

        [data-theme="dark"] .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: var(--text-primary);
        }

        [data-theme="dark"] .select2-dropdown {
            background-color: var(--bg-secondary);
            border-color: var(--border-color);
        }

        [data-theme="dark"] .select2-container--default .select2-results__option {
            background-color: var(--bg-secondary);
            color: var(--text-primary);
        }

        [data-theme="dark"] .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: var(--bg-tertiary);
        }

        /* Smooth transitions */
        * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }

        /* Theme toggle animation */
        .theme-transition {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Custom scrollbar for dark theme */
        [data-theme="dark"] ::-webkit-scrollbar {
            width: 8px;
        }

        [data-theme="dark"] ::-webkit-scrollbar-track {
            background: var(--bg-secondary);
        }

        [data-theme="dark"] ::-webkit-scrollbar-thumb {
            background: var(--bg-tertiary);
            border-radius: 4px;
        }

        [data-theme="dark"] ::-webkit-scrollbar-thumb:hover {
            background: var(--border-color);
        }
    </style>
</head>
<body class="theme-transition">
    <div id="app">
        <!-- Mobile layout: no sidebar/nav, full width content -->
        <main class="py-2 px-0 w-100" style="max-width:100vw;">
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @livewireScripts

    <!-- Theme Management Script -->
    <script>
        // Theme Management System
        class ThemeManager {
            constructor() {
                this.init();
            }

            init() {
                // Load saved theme or default to light
                const savedTheme = localStorage.getItem('theme') || 'light';
                this.applyTheme(savedTheme);
                
                // Listen for system theme changes
                if (savedTheme === 'system') {
                    this.watchSystemTheme();
                }

                // Listen for storage changes (for multi-tab sync)
                window.addEventListener('storage', (e) => {
                    if (e.key === 'theme') {
                        this.applyTheme(e.newValue);
                    }
                });

                // Add theme toggle functionality
                this.addThemeToggleShortcut();
            }

            applyTheme(theme) {
                const html = document.documentElement;
                const body = document.body;

                // Remove existing theme classes
                html.removeAttribute('data-theme');
                body.removeAttribute('data-theme');

                if (theme === 'system') {
                    // Use system preference
                    const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                    html.setAttribute('data-theme', systemTheme);
                    body.setAttribute('data-theme', systemTheme);
                    this.watchSystemTheme();
                } else {
                    // Use selected theme
                    html.setAttribute('data-theme', theme);
                    body.setAttribute('data-theme', theme);
                }

                // Update meta theme-color for mobile browsers
                this.updateMetaThemeColor(theme);

                // Dispatch custom event for other components
                window.dispatchEvent(new CustomEvent('themeChanged', { 
                    detail: { theme: theme } 
                }));

                console.log(`Theme applied: ${theme}`);
            }

            watchSystemTheme() {
                const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
                mediaQuery.addEventListener('change', (e) => {
                    const currentTheme = localStorage.getItem('theme');
                    if (currentTheme === 'system') {
                        this.applyTheme('system');
                    }
                });
            }

            updateMetaThemeColor(theme) {
                let themeColor = '#ffffff'; // Light theme default
                
                if (theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    themeColor = '#212529'; // Dark theme color
                }

                // Update or create meta theme-color tag
                let metaThemeColor = document.querySelector('meta[name="theme-color"]');
                if (!metaThemeColor) {
                    metaThemeColor = document.createElement('meta');
                    metaThemeColor.name = 'theme-color';
                    document.head.appendChild(metaThemeColor);
                }
                metaThemeColor.content = themeColor;
            }

            addThemeToggleShortcut() {
                // Add Ctrl+Shift+T shortcut to toggle theme
                document.addEventListener('keydown', (e) => {
                    if (e.ctrlKey && e.shiftKey && e.key === 'T') {
                        e.preventDefault();
                        this.toggleTheme();
                    }
                });
            }

            toggleTheme() {
                const currentTheme = localStorage.getItem('theme') || 'light';
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                localStorage.setItem('theme', newTheme);
                this.applyTheme(newTheme);
                
                // Show notification
                this.showThemeNotification(newTheme);
            }

            showThemeNotification(theme) {
                // Create temporary notification
                const notification = document.createElement('div');
                notification.className = 'position-fixed top-0 end-0 p-3';
                notification.style.zIndex = '9999';
                notification.innerHTML = `
                    <div class="toast show" role="alert">
                        <div class="toast-header">
                            <i class="fas fa-palette me-2"></i>
                            <strong class="me-auto">Theme Changed</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                        </div>
                        <div class="toast-body">
                            Switched to ${theme} theme
                        </div>
                    </div>
                `;
                
                document.body.appendChild(notification);
                
                // Auto remove after 3 seconds
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }

            // Public method to set theme (for use in settings)
            setTheme(theme) {
                localStorage.setItem('theme', theme);
                this.applyTheme(theme);
            }

            // Public method to get current theme
            getCurrentTheme() {
                return localStorage.getItem('theme') || 'light';
            }
        }

        // Initialize theme manager
        const themeManager = new ThemeManager();

        // Make theme manager globally available
        window.themeManager = themeManager;

        // Global function for compatibility
        window.applyTheme = function(theme) {
            themeManager.setTheme(theme);
        };

        // Document ready
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize any additional theme-related functionality
            console.log('Theme system initialized');
            
            // Add theme indicator to console
            console.log(`%cCurrent theme: ${themeManager.getCurrentTheme()}`, 'color: #0d6efd; font-weight: bold;');
            console.log('%cUse Ctrl+Shift+T to toggle theme', 'color: #6c757d;');
        });

        // Handle page visibility change to sync theme
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                const savedTheme = localStorage.getItem('theme') || 'light';
                themeManager.applyTheme(savedTheme);
            }
        });
    </script>

    <!-- Additional Scripts -->
    @stack('scripts')
</body>
</html>
