<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Heroicons CDN -->
        <script src="https://cdn.jsdelivr.net/npm/heroicons@2.0.18/outline/index.min.js"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .form-container {
                perspective: 1000px;
            }

            .form-wrapper {
                transition: all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
                transform-origin: center;
            }

            .form-wrapper.hidden {
                display: none;
                opacity: 0;
            }

            .form-wrapper.active {
                display: block;
                opacity: 1;
            }

            .input-icon {
                position: absolute;
                left: 12px;
                top: 50%;
                transform: translateY(-50%);
                width: 20px;
                height: 20px;
                color: #9ca3af;
                pointer-events: none;
            }

            .input-with-icon {
                padding-left: 40px;
            }

            .password-toggle {
                position: absolute;
                right: 12px;
                top: 50%;
                transform: translateY(-50%);
                cursor: pointer;
                color: #6b7280;
                transition: color 0.2s;
                background: none;
                border: none;
                padding: 4px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .password-toggle:hover {
                color: #111827;
            }

            .toggle-button {
                transition: all 0.3s ease;
                position: relative;
            }

            .toggle-button.active {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
            }

            .toggle-button:not(.active) {
                background: white;
                color: #667eea;
                border: 2px solid #e5e7eb;
            }

            .form-field {
                animation: slideUp 0.5s ease-out;
            }

            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .card-shadow {
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            }

            .gradient-bg {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }

            .input-field {
                transition: all 0.3s ease;
            }

            .input-field:focus {
                border-color: #667eea;
                box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            }

            @media (max-width: 640px) {
                .form-wrapper {
                    min-height: auto;
                }
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gradient-to-br from-blue-50 via-white to-purple-50">
        <div class="min-h-screen flex flex-col justify-center items-center px-4 py-6 sm:py-0">
            <!-- Logo Section -->
            <div class="mb-8 text-center">
                <a href="/" class="inline-block">
                    <div class="w-16 h-16 rounded-full gradient-bg flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                    </div>
                </a>
                <h1 class="mt-4 text-3xl font-bold text-gray-900">MedClinic System</h1>
                <p class="mt-2 text-gray-600">Healthcare Management Platform</p>
            </div>

            <!-- Main Card -->
            <div class="w-full max-w-md">
                <div class="bg-white rounded-2xl card-shadow overflow-hidden">
                    <!-- Toggle Buttons -->
                    <div class="flex gap-0 p-1.5 bg-gray-100 m-6 rounded-lg">
                        <button
                            onclick="toggleForm('login')"
                            class="toggle-button active flex-1 py-2.5 px-4 rounded-md font-semibold text-sm transition-all duration-300"
                            id="loginToggle"
                        >
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                                Sign In
                            </div>
                        </button>
                        <button
                            onclick="toggleForm('register')"
                            class="toggle-button flex-1 py-2.5 px-4 rounded-md font-semibold text-sm transition-all duration-300"
                            id="registerToggle"
                        >
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                                Sign Up
                            </div>
                        </button>
                    </div>

                    <div class="px-6 pb-8">
                        <!-- Session Status -->
                        @if ($errors->any() || session('status'))
                            <div class="mb-6 p-4 rounded-lg {{ $errors->any() ? 'bg-red-50 border border-red-200' : 'bg-green-50 border border-green-200' }}">
                                @if ($errors->any())
                                    <div class="text-red-700">
                                        @foreach ($errors->all() as $error)
                                            <p class="text-sm">{{ $error }}</p>
                                        @endforeach
                                    </div>
                                @endif
                                @if (session('status'))
                                    <p class="text-green-700 text-sm">{{ session('status') }}</p>
                                @endif
                            </div>
                        @endif

                        <!-- Login Form -->
                        <form method="POST" action="{{ route('login') }}" class="form-wrapper active" id="loginForm">
                            @csrf

                            <div class="space-y-4">
                                <!-- Email -->
                                <div class="form-field relative">
                                    <label for="login_email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                    <div class="relative">
                                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        <input
                                            id="login_email"
                                            class="input-field input-with-icon w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-0"
                                            type="email"
                                            name="email"
                                            value="{{ old('email') }}"
                                            required
                                            autocomplete="username"
                                            placeholder=""
                                        />
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="form-field">
                                    <label for="login_password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                                    <div class="relative">
                                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                        <input
                                            id="login_password"
                                            class="input-field input-with-icon w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-0 pr-10"
                                            type="password"
                                            name="password"
                                            required
                                            autocomplete="current-password"
                                            placeholder="••••••••"
                                        />
                                        <button type="button" class="password-toggle" onclick="togglePasswordVisibility('login_password')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Remember Me -->
                                <div class="flex items-center justify-between pt-2">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="remember" class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500" />
                                        <span class="text-sm text-gray-700">Remember me</span>
                                    </label>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="text-sm text-purple-600 hover:text-purple-700 font-medium">
                                            Forgot password?
                                        </a>
                                    @endif
                                </div>

                                <!-- Submit Button -->
                                <button
                                    type="submit"
                                    class="w-full mt-6 py-2.5 px-4 bg-purple-600 text-black font-semibold rounded-lg hover:bg-purple-700 transition-all duration-300">
                                    Sign In
                                </button>
                            </div>
                        </form>

                        <!-- Register Form -->
                        <form method="POST" action="{{ route('register') }}" class="form-wrapper hidden" id="registerForm">
                            @csrf

                            <div class="space-y-4">
                                <!-- Full Name -->
                                <div class="form-field relative">
                                    <label for="register_name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                    <div class="relative">
                                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <input
                                            id="register_name"
                                            class="input-field input-with-icon w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-0"
                                            type="text"
                                            name="name"
                                            value="{{ old('name') }}"
                                            required
                                            autocomplete="name"
                                            placeholder=""
                                        />
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="form-field relative">
                                    <label for="register_email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                    <div class="relative">
                                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        <input
                                            id="register_email"
                                            class="input-field input-with-icon w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-0"
                                            type="email"
                                            name="email"
                                            value="{{ old('email') }}"
                                            required
                                            autocomplete="username"
                                            placeholder=""
                                        />
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="form-field relative">
                                    <label for="register_password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                                    <div class="relative">
                                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                        <input
                                            id="register_password"
                                            class="input-field input-with-icon w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-0 pr-10"
                                            type="password"
                                            name="password"
                                            required
                                            autocomplete="new-password"
                                            placeholder="••••••••"
                                        />
                                        <button type="button" class="password-toggle" onclick="togglePasswordVisibility('register_password')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Confirm Password -->
                                <div class="form-field relative">
                                    <label for="register_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                                    <div class="relative">
                                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <input
                                            id="register_password_confirmation"
                                            class="input-field input-with-icon w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-0 pr-10"
                                            type="password"
                                            name="password_confirmation"
                                            required
                                            autocomplete="new-password"
                                            placeholder="••••••••"
                                        />
                                        <button type="button" class="password-toggle" onclick="togglePasswordVisibility('register_password_confirmation')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Terms & Conditions -->
                                <label class="flex items-start gap-2 cursor-pointer pt-2">
                                    <input type="checkbox" class="w-4 h-4 mt-1 text-purple-600 border-gray-300 rounded focus:ring-purple-500" required />
                                    <span class="text-sm text-gray-700">I agree to the <a href="#" class="text-purple-600 hover:text-purple-700 font-medium">Terms & Conditions</a></span>
                                </label>

                                <!-- Submit Button -->
                                <button
                                    type="submit"
                                    class="mt-6 py-2 px-6 bg-purple-600 text-black font-medium rounded-lg hover:bg-purple-700 transition-colors"
                                >
                                    Create Account
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex justify-center items-center mt-8 pt-8 border-t border-gray-200">
                    <p class="text-sm text-gray-600">
                        MedClinic System © 2026. All rights reserved.
                    </p>
                </div>
            </div>
        </div>

        <script>
            function toggleForm(formType) {
                const loginForm = document.getElementById('loginForm');
                const registerForm = document.getElementById('registerForm');
                const loginToggle = document.getElementById('loginToggle');
                const registerToggle = document.getElementById('registerToggle');

                if (formType === 'login') {
                    loginForm.classList.remove('hidden');
                    loginForm.classList.add('active');
                    registerForm.classList.add('hidden');
                    registerForm.classList.remove('active');

                    loginToggle.classList.add('active');
                    registerToggle.classList.remove('active');
                } else {
                    registerForm.classList.remove('hidden');
                    registerForm.classList.add('active');
                    loginForm.classList.add('hidden');
                    loginForm.classList.remove('active');

                    registerToggle.classList.add('active');
                    loginToggle.classList.remove('active');
                }
            }

            function togglePasswordVisibility(inputId) {
                const input = document.getElementById(inputId);
                const isPassword = input.type === 'password';

                input.type = isPassword ? 'text' : 'password';

                // You could add visual feedback here
                const button = event.target.closest('.password-toggle');
                if (button) {
                    button.style.opacity = isPassword ? '1' : '0.6';
                }
            }

            // Add smooth scroll behavior
            document.addEventListener('DOMContentLoaded', function() {
                // Preserve form state on page reload
                const formType = sessionStorage.getItem('activeForm') || 'login';
                if (formType === 'register' && new URLSearchParams(window.location.search).has('register')) {
                    toggleForm('register');
                }
            });

            // Save form state before navigation
            window.addEventListener('beforeunload', function() {
                const registerForm = document.getElementById('registerForm');
                if (!registerForm.classList.contains('hidden')) {
                    sessionStorage.setItem('activeForm', 'register');
                } else {
                    sessionStorage.setItem('activeForm', 'login');
                }
            });
        </script>
    </body>
</html>
