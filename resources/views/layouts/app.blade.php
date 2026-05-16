<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Medical Clinic System')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .nav-link { transition: all 0.3s ease; }
        .nav-link:hover { padding-left: 1.5rem; }
        .card { box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.2s; }
        .card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .badge-status { font-size: 0.75rem; padding: 0.25rem 0.75rem; border-radius: 9999px; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .table-hover tbody tr:hover { background-color: rgba(102, 126, 234, 0.05); }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="sidebar w-64 text-white overflow-y-auto">
            <div class="p-6 border-b border-white border-opacity-20">
                <h1 class="text-2xl font-bold flex items-center gap-2">
                    <i class="fas fa-hospital"></i>
                    MedClinic
                </h1>
            </div>
            <nav class="p-4">
                <div class="mb-6">
                    <p class="text-xs font-semibold text-white text-opacity-70 mb-3 uppercase tracking-wider">Main</p>
                    <a href="{{ route('dashboard') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white hover:bg-opacity-10 mb-2">
                        <i class="fas fa-chart-line w-5"></i>
                        <span>Dashboard</span>
                    </a>
                </div>

                <div class="mb-6">
                    <p class="text-xs font-semibold text-white text-opacity-70 mb-3 uppercase tracking-wider">Management</p>
                    <a href="{{ route('patients.index') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white hover:bg-opacity-10 mb-2">
                        <i class="fas fa-users w-5"></i>
                        <span>Patients</span>
                    </a>
                    <a href="{{ route('doctors.index') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white hover:bg-opacity-10 mb-2">
                        <i class="fas fa-user-md w-5"></i>
                        <span>Doctors</span>
                    </a>
                    <a href="{{ route('rooms.index') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white hover:bg-opacity-10 mb-2">
                        <i class="fas fa-door-open w-5"></i>
                        <span>Rooms</span>
                    </a>
                    <a href="{{ route('appointments.index') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white hover:bg-opacity-10 mb-2">
                        <i class="fas fa-calendar w-5"></i>
                        <span>Appointments</span>
                    </a>
                </div>

                <div class="mb-6">
                    <p class="text-xs font-semibold text-white text-opacity-70 mb-3 uppercase tracking-wider">Operations</p>
                    <a href="{{ route('inventories.index') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white hover:bg-opacity-10 mb-2">
                        <i class="fas fa-box w-5"></i>
                        <span>Inventory</span>
                    </a>
                    <a href="{{ route('transactions.index') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white hover:bg-opacity-10 mb-2">
                        <i class="fas fa-receipt w-5"></i>
                        <span>Transactions</span>
                    </a>
                    <a href="{{ route('transactions.report') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white hover:bg-opacity-10">
                        <i class="fas fa-file-alt w-5"></i>
                        <span>Reports</span>
                    </a>
                </div>

                <div class="mt-auto pt-6 border-t border-white border-opacity-20">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white hover:bg-opacity-10 w-full text-left">
                            <i class="fas fa-sign-out-alt w-5"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white border-b border-gray-200 px-8 py-4 flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <h2 class="text-2xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-sm text-gray-600">{{ auth()->user()->name ?? 'User' }}</p>
                        <p class="text-xs text-gray-500">{{ now()->format('M d, Y') }}</p>
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <section class="flex-1 overflow-y-auto p-8">
                <!-- Alerts -->
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <h3 class="text-red-800 font-semibold mb-2">Validation Errors</h3>
                        <ul class="text-red-700 list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex justify-between items-center">
                        <span class="text-green-800 flex items-center gap-2">
                            <i class="fas fa-check-circle"></i>
                            {{ session('success') }}
                        </span>
                        <button type="button" onclick="this.parentElement.style.display='none'" class="text-green-600 hover:text-green-800">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex justify-between items-center">
                        <span class="text-red-800 flex items-center gap-2">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ session('error') }}
                        </span>
                        <button type="button" onclick="this.parentElement.style.display='none'" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @yield('content')
            </section>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 5000);
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
