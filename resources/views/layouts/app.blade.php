<!DOCTYPE html>
<html lang="id">
<head>
    <style>
    input::-ms-reveal,
    input::-ms-clear {
        display: none !important;
    }
</style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIPANDAI') - PT PLN (Persero) UPT Gandul</title>

    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        pln: {
                            blue: '#0084c7',
                            hover: '#0275b0',
                            navy: '#004777',
                            dark: '#002b49',
                            light: '#e0f2fe',
                            subtle: '#f0f9ff',
                            yellow: '#f59e0b',
                            red: '#ef4444',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            font-size: 15px;
        }
        @media print {
            .no-print, #sidebar-backdrop, #main-sidebar, aside, header {
                display: none !important;
                visibility: hidden !important;
                height: 0 !important;
            }
            .print-only {
                display: block !important;
            }
            html, body {
                background-color: white !important;
                background: white !important;
                height: auto !important;
                min-height: 0 !important;
                overflow: visible !important;
            }
            .min-h-screen, .flex, .flex-1, .flex-col, .overflow-x-hidden {
                display: block !important;
                height: auto !important;
                min-height: 0 !important;
                overflow: visible !important;
            }
            main {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="h-screen text-slate-800 antialiased overflow-hidden {{ request()->routeIs('login') ? '' : '' }}">

    @if(request()->routeIs('login'))
        <!-- Tampilan Halaman Login (Full Screen Split View) -->
        <main class="w-full h-screen p-0 m-0 overflow-hidden flex">
            @yield('content')
        </main>
    @elseif(Auth::check())
        <!-- Tampilan Dashboard & Aplikasi dengan SIDEBAR -->
        <div class="h-screen w-full flex flex-row bg-slate-50 overflow-hidden">
            
            <!-- Backdrop Mobile Drawer -->
            <div id="sidebar-backdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-40 lg:hidden hidden transition-opacity duration-300 no-print"></div>

            <!-- SIDEBAR KIRI (Dibuat Sticky & Tinggi 1 Layar Penuh) -->
            <aside id="main-sidebar" class="absolute lg:static inset-y-0 left-0 z-50 w-72 h-full bg-white border-r border-slate-200 flex flex-col justify-between transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0 shrink-0 shadow-sm no-print overflow-y-auto">
                
               <!-- Header Sidebar: Logo PLN & Brand Title -->
               <div>
                    <div class="p-5 border-b border-slate-100 flex items-center space-x-3">
                        <a href="{{ url('/') }}" class="flex items-center space-x-3 group min-w-0">
                            <img src="{{ asset('images/logo-pln.png') }}" alt="Logo PLN" class="h-10 w-auto object-contain shrink-0">
                            <div class="leading-tight min-w-0">
                                <span class="block text-base font-extrabold tracking-tight text-pln-navy group-hover:text-pln-blue transition truncate">SIPANDAI</span>
                            </div>
                        </a>
                        <button onclick="toggleSidebar()" type="button" class="lg:hidden p-1.5 text-slate-400 hover:text-slate-600 rounded-lg focus:outline-none ml-auto">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>

                    <!-- Menu Navigasi Utama -->
                    <div class="px-3.5 py-5 space-y-2">
                        <div class="px-3 pb-2 text-sm font-bold uppercase tracking-wider text-slate-400">
                            @if(Auth::user()->isAdmin())
                                Menu Administrator
                            @else
                                Menu Pegawai
                            @endif
                        </div>

                        @if(Auth::user()->isAdmin())
                            <!-- Dashboard Admin -->
                            <a href="{{ route('admin.dashboard') }}" 
                               class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm sm:text-base font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'bg-pln-blue text-white shadow-sm font-bold' : 'text-slate-700 hover:text-pln-blue hover:bg-slate-50' }}">
                                <i class="fa-solid fa-chart-pie w-6 text-center {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-pln-blue' }}"></i>
                                <span>Dashboard</span>
                            </a>

                            <!-- Kelola Pengaduan -->
                            <a href="{{ route('admin.complaints.index') }}" 
                               class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm sm:text-base font-semibold transition {{ request()->routeIs('admin.complaints.*') ? 'bg-pln-blue text-white shadow-sm font-bold' : 'text-slate-700 hover:text-pln-blue hover:bg-slate-50' }}">
                                <i class="fa-solid fa-list-check w-6 text-center {{ request()->routeIs('admin.complaints.*') ? 'text-white' : 'text-pln-blue' }}"></i>
                                <span>Kelola Pengaduan</span>
                            </a>

                            <!-- Kategori -->
                            <a href="{{ route('admin.categories.index') }}" 
                               class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm sm:text-base font-semibold transition {{ request()->routeIs('admin.categories.*') ? 'bg-pln-blue text-white shadow-sm font-bold' : 'text-slate-700 hover:text-pln-blue hover:bg-slate-50' }}">
                                <i class="fa-solid fa-tags w-6 text-center {{ request()->routeIs('admin.categories.*') ? 'text-white' : 'text-pln-blue' }}"></i>
                                <span>Kategori</span>
                            </a>

                            <!-- Pegawai -->
                            <a href="{{ route('admin.employees.index') }}" 
                               class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm sm:text-base font-semibold transition {{ request()->routeIs('admin.employees.*') ? 'bg-pln-blue text-white shadow-sm font-bold' : 'text-slate-700 hover:text-pln-blue hover:bg-slate-50' }}">
                                <i class="fa-solid fa-users w-6 text-center {{ request()->routeIs('admin.employees.*') ? 'text-white' : 'text-pln-blue' }}"></i>
                                <span>Pegawai</span>
                            </a>

                            <!-- Laporan -->
                            <a href="{{ route('admin.reports.index') }}" 
                               class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm sm:text-base font-semibold transition {{ request()->routeIs('admin.reports.*') ? 'bg-pln-blue text-white shadow-sm font-bold' : 'text-slate-700 hover:text-pln-blue hover:bg-slate-50' }}">
                                <i class="fa-solid fa-file-invoice w-6 text-center {{ request()->routeIs('admin.reports.*') ? 'text-white' : 'text-pln-blue' }}"></i>
                                <span>Laporan</span>
                            </a>

                            <a href="{{ route('admin.profile') }}" 
   class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm sm:text-base font-semibold transition {{ request()->routeIs('admin.profile') ? 'bg-pln-blue text-white shadow-sm font-bold' : 'text-slate-700 hover:text-pln-blue hover:bg-slate-50' }}">
    <i class="fa-regular fa-circle-user w-6 text-center {{ request()->routeIs('admin.profile') ? 'text-white' : 'text-pln-blue' }}"></i>
    <span>Profil Saya</span>
</a>
                        @else
                            <!-- Dashboard Pegawai -->
                            <a href="{{ route('employee.dashboard') }}" 
                               class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm sm:text-base font-semibold transition {{ request()->routeIs('employee.dashboard') ? 'bg-pln-blue text-white shadow-sm font-bold' : 'text-slate-700 hover:text-pln-blue hover:bg-slate-50' }}">
                                <i class="fa-solid fa-house w-6 text-center {{ request()->routeIs('employee.dashboard') ? 'text-white' : 'text-pln-blue' }}"></i>
                                <span>Dashboard</span>
                            </a>

                            <!-- Buat Pengaduan -->
                            <a href="{{ route('employee.complaints.create') }}" 
                               class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm sm:text-base font-semibold transition {{ request()->routeIs('employee.complaints.create') ? 'bg-pln-navy text-white shadow-sm font-bold' : 'text-slate-700 hover:text-pln-blue hover:bg-slate-50' }}">
                                <i class="fa-solid fa-circle-plus w-6 text-center {{ request()->routeIs('employee.complaints.create') ? 'text-white' : 'text-pln-blue' }}"></i>
                                <span>Buat Pengaduan</span>
                            </a>

                            <!-- Pengaduan Saya -->
                            <a href="{{ route('employee.complaints.index') }}" 
                               class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm sm:text-base font-semibold transition {{ request()->routeIs('employee.complaints.index') || request()->routeIs('employee.complaints.show') ? 'bg-pln-blue text-white shadow-sm font-bold' : 'text-slate-700 hover:text-pln-blue hover:bg-slate-50' }}">
                                <i class="fa-solid fa-clipboard-list w-6 text-center {{ request()->routeIs('employee.complaints.index') || request()->routeIs('employee.complaints.show') ? 'text-white' : 'text-pln-blue' }}"></i>
                                <span>Pengaduan Saya</span>
                            </a>

                            <!-- Lacak Tiket -->
                            <a href="{{ route('complaints.track') }}" 
                               class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm sm:text-base font-semibold transition {{ request()->routeIs('complaints.track') ? 'bg-pln-blue text-white shadow-sm font-bold' : 'text-slate-700 hover:text-pln-blue hover:bg-slate-50' }}">
                                <i class="fa-solid fa-magnifying-glass-location w-6 text-center {{ request()->routeIs('complaints.track') ? 'text-white' : 'text-pln-blue' }}"></i>
                                <span>Lacak Tiket</span>
                            </a>

                            <!-- Profil Pegawai -->
                            <a href="{{ route('employee.profile') }}" 
                               class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm sm:text-base font-semibold transition {{ request()->routeIs('employee.profile') ? 'bg-pln-blue text-white shadow-sm font-bold' : 'text-slate-700 hover:text-pln-blue hover:bg-slate-50' }}">
                                <i class="fa-regular fa-circle-user w-6 text-center {{ request()->routeIs('employee.profile') ? 'text-white' : 'text-pln-blue' }}"></i>
                                <span>Profil Saya</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Bagian Bawah Sidebar: Profil Pengguna & Logout -->
                <div class="p-5 border-t border-slate-100 bg-slate-50/60">
                    <div class="flex items-center space-x-3 mb-3.5 p-3 rounded-xl bg-white border border-slate-200/80 shadow-xs">
                        <div class="w-11 h-11 rounded-xl bg-pln-light text-pln-navy flex items-center justify-center font-extrabold text-base shrink-0 border border-pln-blue/20">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-bold text-slate-800 truncate" title="{{ Auth::user()->name }}">{{ Auth::user()->name }}</div>
                           <div class="text-sm text-slate-500 truncate flex items-center mt-0.5">
                                @if(Auth::user()->isAdmin())
                                    <span class="text-slate-500 font-medium">Administrator UPT Gandul</span>
                                @else
                                    <span class="text-slate-500 font-mono font-medium">{{ Auth::user()->nip ?: 'Pegawai' }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?')"
                                class="w-full flex items-center justify-center space-x-2 px-4 py-2.5 rounded-xl text-sm font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            <span>Keluar Sistem</span>
                        </button>
                    </form>
                </div>
            </aside>

            <!-- AREA KONTEN UTAMA (Dibuat Scrollable Mandiri) -->
            <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto bg-slate-50">
                
                <!-- Top Header Bar -->
                <header class="bg-white border-b border-slate-200 sticky top-0 z-30 px-8 lg:px-12 h-20 flex items-center justify-between no-print shadow-xs w-full shrink-0">
                    <div class="flex items-center space-x-4">
                        <button onclick="toggleSidebar()" type="button" 
                                class="lg:hidden p-2.5 rounded-xl text-slate-500 hover:text-slate-700 hover:bg-slate-100 border border-slate-200 focus:outline-none">
                            <i class="fa-solid fa-bars text-lg"></i>
                        </button>
                        <!-- Tanggal & Status Sistem -->
                        <div class="flex items-center space-x-4 text-sm sm:text-base text-slate-700 font-semibold">
                            <span class="hidden sm:inline-flex items-center">
                                <i class="fa-regular fa-calendar text-pln-blue mr-2 text-lg"></i> {{ now()->format('d M Y') }}
                            </span>
                            <span class="hidden sm:inline text-slate-300">|</span>
                            <span class="inline-flex items-center text-emerald-600 font-bold bg-emerald-50 px-3.5 py-1.5 rounded-xl border border-emerald-200 text-sm">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mr-2 animate-pulse"></span> Sistem Aktif
                            </span>
                        </div>
                    </div>

                    <!-- Sisi Kanan Topbar -->
                    <div class="flex items-center space-x-3">
                        @if(Auth::user()->isAdmin())
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-pln-navy text-white shadow-xs">
                                <i class="fa-solid fa-shield-halved text-pln-yellow mr-2 text-base"></i> Admin UPT Gandul
                            </span>
                        @else
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-pln-light text-pln-navy border border-pln-blue/20">
                                <i class="fa-solid fa-id-badge text-pln-blue mr-2 text-base"></i> {{ Auth::user()->nip ?: 'Pegawai' }}
                            </span>
                        @endif
                    </div>
                </header>

                <!-- Flash Messages & Page Content -->
                <main class="flex-1 py-6 px-8 lg:px-12 w-full max-w-none">
                    @if (session('success'))
                        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-6 py-4 rounded-2xl flex items-start space-x-3 shadow-sm text-base font-semibold" role="alert">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-xl mt-0.5"></i>
                            <div class="flex-1 leading-relaxed">{{ session('success') }}</div>
                        </div>
                    @endif

                    @if (session('info'))
                        <div class="mb-6 bg-sky-50 border border-sky-200 text-sky-800 px-6 py-4 rounded-2xl flex items-start space-x-3 shadow-sm text-base font-semibold" role="alert">
                            <i class="fa-solid fa-circle-info text-sky-600 text-xl mt-0.5"></i>
                            <div class="flex-1 leading-relaxed">{{ session('info') }}</div>
                        </div>
                    @endif

                    @if (session('error') || (isset($errors) && $errors->any()))
                        <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-6 py-4 rounded-2xl flex items-start space-x-3 shadow-sm text-base" role="alert">
                            <i class="fa-solid fa-circle-exclamation text-rose-600 text-xl mt-0.5"></i>
                            <div class="flex-1 leading-relaxed">
                                @if(session('error'))
                                    <div class="font-bold">{{ session('error') }}</div>
                                @endif
                                @if($errors->any())
                                    <ul class="list-disc list-inside mt-1.5 space-y-1 font-semibold">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    @endif

                    @yield('content')
                </main>

                <!-- Footer Profesional & Rapi -->
                <footer class="bg-white border-t border-slate-200 py-6 px-8 lg:px-12 text-slate-600 text-sm no-print flex flex-col sm:flex-row justify-between items-center gap-4 w-full mt-auto shrink-0 shadow-inner">
                    <div class="flex items-center space-x-3">
                        <img src="{{ asset('images/logo-pln.png') }}" alt="Logo PLN" class="h-6 object-contain ">
                        <span class="font-bold text-slate-800">SIPANDAI &bull; Sistem Pengaduan Antar Dan Internal</span>
                    </div>
                    <div class="font-medium text-slate-500">
                        &copy; {{ date('Y') }} PT PLN (Persero) UIT JBB &bull; UPT Gandul. Hak Cipta Dilindungi.
                    </div>
                </footer>
            </div>
        </div>
    @else
        <!-- Tampilan untuk Pengunjung/Guest yang Belum Login -->
        <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm no-print w-full">
            <div class="w-full px-8 lg:px-12">
                <div class="flex justify-between items-center h-20">
                    <a href="{{ url('/') }}" class="flex items-center space-x-3">
                        <div class="w-11 h-11 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center justify-center p-2 shrink-0">
                            <img src="{{ asset('images/logo-pln.png') }}" alt="Logo PLN" class="w-full h-full object-contain">
                        </div>
                        <div>
                            <span class="block text-xl font-extrabold tracking-tight text-pln-navy">SIPANDAI</span>
                            <span class="block text-sm text-slate-500 font-medium leading-tight">PT PLN (Persero) UPT Gandul</span>
                        </div>
                    </a>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('complaints.track') }}" class="text-sm font-bold text-slate-700 hover:text-pln-blue px-4 py-2.5 rounded-xl">
                            <i class="fa-solid fa-magnifying-glass-location mr-1.5"></i> Lacak Tiket
                        </a>
                        <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl bg-pln-blue hover:bg-pln-hover text-white text-sm font-bold transition shadow-sm">
                            <i class="fa-solid fa-arrow-right-to-bracket mr-1.5"></i> Masuk
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-grow w-full px-8 lg:px-12 py-8 max-w-none">
            @if (session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-6 py-4 rounded-2xl flex items-start space-x-3 shadow-sm text-base font-semibold" role="alert">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-xl mt-0.5"></i>
                    <div class="flex-1 leading-relaxed">{{ session('success') }}</div>
                </div>
            @endif

            @if (session('info'))
                <div class="mb-6 bg-sky-50 border border-sky-200 text-sky-800 px-6 py-4 rounded-2xl flex items-start space-x-3 shadow-sm text-base font-semibold" role="alert">
                    <i class="fa-solid fa-circle-info text-sky-600 text-xl mt-0.5"></i>
                    <div class="flex-1 leading-relaxed">{{ session('info') }}</div>
                </div>
            @endif

            @if (session('error') || (isset($errors) && $errors->any()))
                <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-6 py-4 rounded-2xl flex items-start space-x-3 shadow-sm text-base" role="alert">
                    <i class="fa-solid fa-circle-exclamation text-rose-600 text-xl mt-0.5"></i>
                    <div class="flex-1 leading-relaxed">
                        @if(session('error'))
                            <div class="font-bold">{{ session('error') }}</div>
                        @endif
                        @if($errors->any())
                            <ul class="list-disc list-inside mt-1.5 space-y-1 font-semibold">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="bg-white border-t border-slate-200 py-6 mt-auto text-slate-600 text-sm no-print w-full shadow-inner">
            <div class="w-full px-8 lg:px-12 flex flex-col sm:flex-row justify-between items-center gap-4 text-center sm:text-left">
                <div class="flex items-center space-x-2.5">
                    <img src="{{ asset('images/logo-pln.png') }}" alt="Logo PLN" class="h-6 object-contain ">
                    <span class="font-bold text-slate-800">SIPANDAI &bull; Sistem Pengaduan Antar Dan Internal</span>
                </div>
                <div class="font-medium text-slate-500">
                    &copy; {{ date('Y') }} PT PLN (Persero) UIT JBB &bull; UPT Gandul. Hak Cipta Dilindungi.
                </div>
            </div>
        </footer>
    @endif

    <!-- Mobile Drawer Toggle Script -->
    <script>
        function toggleSidebar`() {
            const sidebar = document.getElementById('main-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            if (sidebar && backdrop) {
                sidebar.classList.toggle('-translate-x-full');
                backdrop.classList.toggle('hidden');
            }
        }
    </script>

    <!-- Floating Chatbot AI Assistant -->
    @auth
        @include('components.chatbot-widget')
    @endauth

    @stack('scripts')

    <script>
        window.addEventListener("pageshow", function (event) {
            if (event.persisted || (performance.getEntriesByType("navigation")[0].type === "back_forward")) {
                window.location.reload();
            }
        });
    </script>
</body>
</html>