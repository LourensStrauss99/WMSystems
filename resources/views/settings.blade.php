<?php
namespace App\Http\Controllers;  

use Illuminate\Http\Request;

class PhoneController extends Controller
{
    public function index()
    {
        return view('settings'); // or your actual settings view
    }
}
?>

{{-- filepath: resources/views/settings.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="text-dark fw-bold mb-1">
                <i class="fas fa-cog text-primary me-2"></i>
                Settings
            </h2>
            <p class="text-muted">Manage your account settings and preferences</p>
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#settingsSidebar" aria-expanded="true" aria-controls="settingsSidebar">
                <i class="fas fa-bars me-2"></i>Toggle Sidebar
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8" id="mainContent">
            <!-- Phone Verification Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-mobile-alt text-success me-2"></i>Phone Verification
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">Verify your phone number to enable SMS notifications and two-factor authentication.</p>
                    
                    <form id="verificationForm" autocomplete="off">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phoneNumber" class="form-label fw-bold text-muted">Phone Number</label>
                                    <input type="text" id="phoneNumber" class="form-control" 
                                           placeholder="+27721234567" required>
                                    <small class="form-text text-muted">Include country code (e.g., +27 for South Africa)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted">&nbsp;</label>
                                <button type="button" class="btn btn-primary text-white d-block" onclick="sendCode()">
                                    <i class="fas fa-paper-plane me-2"></i>Send Verification Code
                                </button>
                            </div>
                        </div>
                        
                        <div id="otpSection" style="display: none;" class="border-top pt-4 mt-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="otpCode" class="form-label fw-bold text-muted">Enter 6-digit Code</label>
                                        <input type="text" id="otpCode" class="form-control" maxlength="6" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted">&nbsp;</label>
                                    <button type="submit" class="btn btn-success text-white d-block">
                                        <i class="fas fa-check me-2"></i>Verify Code
                                    </button>
                                </div>
                            </div>
                            
                            <div id="timer" class="alert alert-info d-flex align-items-center" style="display: none;">
                                <i class="fas fa-clock me-2"></i>
                                <span id="timerText">Time left: 5:00</span>
                            </div>
                        </div>
                        
                        <div id="message" class="mt-3"></div>
                    </form>
                </div>
            </div>

            <!-- Appearance Settings Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-palette text-info me-2"></i>Appearance
                    </h5>
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#appearanceSettings" aria-expanded="true" aria-controls="appearanceSettings">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                <div class="collapse show" id="appearanceSettings">
                    <div class="card-body">
                        <p class="text-muted mb-4">Choose your preferred theme for the interface.</p>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <label class="form-label fw-bold text-muted mb-3">Theme Selection</label>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="card border theme-option" data-theme="light">
                                            <div class="card-body text-center p-4 bg-light">
                                                <div class="theme-icon mb-3">
                                                    ☀️
                                                </div>
                                                <h6 class="card-title fw-bold">Light</h6>
                                                <p class="card-text small text-muted mb-3">Bright and clean interface</p>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="theme" id="lightTheme" value="light" checked>
                                                    <label class="form-check-label fw-bold" for="lightTheme">Select</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card border theme-option" data-theme="dark">
                                            <div class="card-body text-center p-4 bg-dark text-white">
                                                <div class="theme-icon mb-3">
                                                    🌙
                                                </div>
                                                <h6 class="card-title fw-bold">Dark</h6>
                                                <p class="card-text small text-light mb-3">Easy on the eyes</p>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="theme" id="darkTheme" value="dark">
                                                    <label class="form-check-label text-white fw-bold" for="darkTheme">Select</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card border theme-option" data-theme="system">
                                            <div class="card-body text-center p-4 bg-gradient">
                                                <div class="theme-icon mb-3">
                                                    🖥️
                                                </div>
                                                <h6 class="card-title fw-bold">System</h6>
                                                <p class="card-text small text-muted mb-3">Follow device preference</p>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="theme" id="systemTheme" value="system">
                                                    <label class="form-check-label fw-bold" for="systemTheme">Select</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="border-top pt-4 mt-4">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <button class="btn btn-primary text-white" onclick="saveThemeSettings()">
                                                <i class="fas fa-save me-2"></i>Apply Theme
                                            </button>
                                            <button class="btn btn-outline-secondary ms-2" onclick="resetTheme()">
                                                <i class="fas fa-undo me-2"></i>Reset
                                            </button>
                                        </div>
                                        <small class="text-muted">Changes will be applied immediately</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notification Settings Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bell text-warning me-2"></i>Notification Settings
                    </h5>
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#notificationSettings" aria-expanded="true" aria-controls="notificationSettings">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                <div class="collapse show" id="notificationSettings">
                    <div class="card-body">
                        <p class="text-muted mb-4">Configure how you receive notifications and alerts.</p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                                    <label class="form-check-label" for="emailNotifications">
                                        <i class="fas fa-envelope me-2 text-primary"></i>Email Notifications
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="smsNotifications">
                                    <label class="form-check-label" for="smsNotifications">
                                        <i class="fas fa-sms me-2 text-success"></i>SMS Notifications
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="taskReminders" checked>
                                    <label class="form-check-label" for="taskReminders">
                                        <i class="fas fa-tasks me-2 text-info"></i>Task Reminders
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="systemAlerts" checked>
                                    <label class="form-check-label" for="systemAlerts">
                                        <i class="fas fa-exclamation-triangle me-2 text-warning"></i>System Alerts
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="border-top pt-3 mt-3">
                            <button class="btn btn-primary text-white" onclick="saveNotificationSettings()">
                                <i class="fas fa-save me-2"></i>Save Preferences
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Collapsible Sidebar -->
        <div class="col-lg-4 collapse show" id="settingsSidebar">
            <!-- Quick Actions Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt text-primary me-2"></i>Quick Actions
                    </h5>
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#quickActions" aria-expanded="true" aria-controls="quickActions">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                <div class="collapse show" id="quickActions">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('master.settings') }}" class="btn btn-primary text-white">
                                <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
                            </a>
                            <a href="{{ route('settings.profile') }}" class="btn btn-outline-info">
                                <i class="fas fa-user me-2"></i>Edit Profile
                            </a>
                            <a href="{{ route('settings.password') }}" class="btn btn-outline-warning">
                                <i class="fas fa-key me-2"></i>Change Password
                            </a>
                            <div class="dropdown">
                                <button class="btn btn-outline-danger dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-sign-out-alt me-2"></i>Account
                                </button>
                                <ul class="dropdown-menu w-100">
                                    <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2 text-danger"></i>Logout
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="confirmDelete()">
                                        <i class="fas fa-trash me-2"></i>Delete Account
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Info Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle text-info me-2"></i>Account Information
                    </h5>
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#accountInfo" aria-expanded="true" aria-controls="accountInfo">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                <div class="collapse show" id="accountInfo">
                    <div class="card-body">
                        <div class="row text-sm mb-2">
                            <div class="col-6"><i class="fas fa-user text-muted me-2"></i>Name:</div>
                            <div class="col-6 text-end fw-bold">{{ auth()->user()->name ?? 'N/A' }}</div>
                        </div>
                        <div class="row text-sm mb-2">
                            <div class="col-6"><i class="fas fa-envelope text-muted me-2"></i>Email:</div>
                            <div class="col-6 text-end">{{ auth()->user()->email ?? 'N/A' }}</div>
                        </div>
                        <div class="row text-sm mb-2">
                            <div class="col-6"><i class="fas fa-calendar text-muted me-2"></i>Joined:</div>
                            <div class="col-6 text-end">{{ auth()->user()->created_at->format('M Y') ?? 'N/A' }}</div>
                        </div>
                        <div class="row text-sm">
                            <div class="col-6"><i class="fas fa-shield-alt text-muted me-2"></i>Role:</div>
                            <div class="col-6 text-end">
                                <span class="badge bg-primary">{{ auth()->user()->role ?? 'User' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Status Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-shield-alt text-success me-2"></i>Security Status
                    </h5>
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#securityStatus" aria-expanded="true" aria-controls="securityStatus">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                <div class="collapse show" id="securityStatus">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <i class="fas fa-mobile-alt me-2 text-warning"></i>
                                <span>Phone Verified</span>
                            </div>
                            <span class="badge bg-warning text-dark">Pending</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <i class="fas fa-envelope me-2 text-success"></i>
                                <span>Email Verified</span>
                            </div>
                            <span class="badge bg-success">Verified</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-key me-2 text-info"></i>
                                <span>Strong Password</span>
                            </div>
                            <span class="badge bg-info">Active</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden logout form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<script>
let attempts = 0;
let maxAttempts = 3;
let timer;
let timeLeft = 300; // 5 minutes in seconds

// Toggle sidebar and adjust main content width
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.querySelector('[data-bs-target="#settingsSidebar"]');
    const mainContent = document.getElementById('mainContent');
    const sidebar = document.getElementById('settingsSidebar');
    
    sidebar.addEventListener('shown.bs.collapse', function() {
        mainContent.className = 'col-lg-8';
        sidebarToggle.innerHTML = '<i class="fas fa-bars me-2"></i>Hide Sidebar';
    });
    
    sidebar.addEventListener('hidden.bs.collapse', function() {
        mainContent.className = 'col-lg-12';
        sidebarToggle.innerHTML = '<i class="fas fa-bars me-2"></i>Show Sidebar';
    });
    
    // Rotate chevron icons on collapse
    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(button => {
        const target = button.getAttribute('data-bs-target');
        const targetElement = document.querySelector(target);
        
        if (targetElement && button.querySelector('.fa-chevron-down')) {
            targetElement.addEventListener('shown.bs.collapse', function() {
                button.querySelector('.fa-chevron-down').style.transform = 'rotate(180deg)';
            });
            
            targetElement.addEventListener('hidden.bs.collapse', function() {
                button.querySelector('.fa-chevron-down').style.transform = 'rotate(0deg)';
            });
        }
    });

    // Theme selection highlighting
    document.querySelectorAll('.theme-option').forEach(option => {
        option.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
            
            // Remove active class from all options
            document.querySelectorAll('.theme-option').forEach(opt => {
                opt.classList.remove('border-primary', 'border-3');
            });
            
            // Add active class to selected option
            this.classList.add('border-primary', 'border-3');
        });
    });

    // Load saved theme preference
    const savedTheme = localStorage.getItem('theme') || 'light';
    const savedThemeElement = document.querySelector(`input[value="${savedTheme}"]`);
    if (savedThemeElement) {
        savedThemeElement.checked = true;
        savedThemeElement.closest('.theme-option').classList.add('border-primary', 'border-3');
    }
});

function startTimer() {
    clearInterval(timer);
    timeLeft = 300;
    updateTimerDisplay();
    document.getElementById('timer').style.display = 'block';
    
    timer = setInterval(() => {
        timeLeft--;
        updateTimerDisplay();
        if (timeLeft <= 0) {
            clearInterval(timer);
            showMessage("Verification time expired. Please request a new code.", "danger");
            document.getElementById('otpSection').style.display = 'none';
            document.getElementById('timer').style.display = 'none';
            attempts = 0;
        }
    }, 1000);
}

function updateTimerDisplay() {
    const timerText = document.getElementById('timerText');
    if (timerText) {
        const min = Math.floor(timeLeft / 60);
        const sec = timeLeft % 60;
        timerText.textContent = `Time left: ${min}:${sec.toString().padStart(2, '0')}`;
    }
}

function showMessage(message, type = 'info') {
    const messageElement = document.getElementById('message');
    const alertClass = type === 'danger' ? 'alert-danger' : 
                     type === 'success' ? 'alert-success' : 'alert-info';
    
    const iconClass = type === 'danger' ? 'fas fa-exclamation-triangle' : 
                     type === 'success' ? 'fas fa-check-circle' : 'fas fa-info-circle';
    
    messageElement.innerHTML = `
        <div class="alert ${alertClass} d-flex align-items-center" role="alert">
            <i class="${iconClass} me-2"></i>
            ${message}
        </div>
    `;
}

async function sendCode() {
    const phoneNumber = document.getElementById('phoneNumber').value;
    const otpSection = document.getElementById('otpSection');

    if (!phoneNumber) {
        showMessage("Please enter a phone number.", "danger");
        return;
    }

    // Simulate backend response
    showMessage("A 6-digit code has been sent to your phone. Please enter it below.", "info");
    otpSection.style.display = 'block';
    attempts = 0;
    startTimer();
    console.log("Simulated OTP sent to " + phoneNumber + " (123456)");
}

document.getElementById('verificationForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const otpCode = document.getElementById('otpCode').value;

    if (timeLeft <= 0) {
        showMessage("Verification time expired. Please request a new code.", "danger");
        document.getElementById('otpSection').style.display = 'none';
        return;
    }

    if (!otpCode || otpCode.length !== 6) {
        showMessage("Please enter a valid 6-digit code.", "danger");
        return;
    }

    attempts++;
    if (otpCode === "123456") { // Replace with actual OTP comparison from backend
        showMessage("Phone verification successful! Your phone number has been verified.", "success");
        clearInterval(timer);
        document.getElementById('timer').style.display = 'none';
        
        // Update security status
        setTimeout(() => {
            location.reload();
        }, 2000);
    } else {
        if (attempts >= maxAttempts) {
            showMessage("Maximum attempts reached. Please request a new code.", "danger");
            document.getElementById('otpSection').style.display = 'none';
            document.getElementById('timer').style.display = 'none';
            clearInterval(timer);
        } else {
            showMessage(`Invalid code. You have ${maxAttempts - attempts} attempt(s) left.`, "danger");
        }
    }
});

// Add delete account confirmation function
function confirmDelete() {
    if (confirm('Are you sure you want to delete your account? This action cannot be undone and will permanently remove all your data.')) {
        if (confirm('This will delete ALL your data permanently. Are you absolutely sure?')) {
            // Add your delete account logic here
            alert('Account deletion would be processed here.');
        }
    }
}

// Save theme settings
function saveThemeSettings() {
    const selectedTheme = document.querySelector('input[name="theme"]:checked').value;
    window.themeManager.setTheme(selectedTheme);
    showMessage(`Theme changed to ${selectedTheme} successfully!`, "success");
}

// Reset theme to default
function resetTheme() {
    // Reset to light theme
    document.querySelector('input[value="light"]').checked = true;
    
    // Remove all active classes
    document.querySelectorAll('.theme-option').forEach(opt => {
        opt.classList.remove('border-primary', 'border-3');
    });
    
    // Add active class to light theme
    document.querySelector('input[value="light"]').closest('.theme-option').classList.add('border-primary', 'border-3');
    
    // Apply light theme
    applyTheme('light');
    localStorage.setItem('theme', 'light');
    
    showMessage("Theme reset to light successfully!", "success");
}

// Apply theme function (placeholder for now)
function applyTheme(theme) {
    // This will be implemented when we work on layout.app
    document.body.setAttribute('data-theme', theme);
    
    if (theme === 'system') {
        // Use system preference
        const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        document.body.setAttribute('data-theme', systemTheme);
    }
}

// Save notification preferences
function saveNotificationSettings() {
    const emailNotifications = document.getElementById('emailNotifications').checked;
    const smsNotifications = document.getElementById('smsNotifications').checked;
    const taskReminders = document.getElementById('taskReminders').checked;
    const systemAlerts = document.getElementById('systemAlerts').checked;
    
    // Simulate saving preferences
    showMessage("Notification preferences saved successfully!", "success");
    
    console.log('Saved settings:', {
        emailNotifications,
        smsNotifications,
        taskReminders,
        systemAlerts
    });
}
</script>

<style>
/* Smooth transitions for collapse animations */
.collapse {
    transition: all 0.3s ease;
}

.fa-chevron-down {
    transition: transform 0.3s ease;
}

/* Custom switch styling */
.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

/* Card hover effects */
.card:hover {
    box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.15) !important;
    transition: box-shadow 0.15s ease-in-out;
}

/* Theme option styling */
.theme-option {
    cursor: pointer;
    transition: all 0.3s ease;
}

.theme-option:hover {
    transform: translateY(-2px);
}

.theme-option.border-primary {
    box-shadow: 0 0.25rem 0.75rem rgba(13, 110, 253, 0.25);
}

/* Theme icon styling */
.theme-icon {
    font-size: 2.5rem;
    line-height: 1;
}

/* Gradient background for system theme */
.bg-gradient {
    background: linear-gradient(45deg, #f8f9fa, #e9ecef);
}
</style>
@endsection

