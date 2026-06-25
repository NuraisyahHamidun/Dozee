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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-900 selection:bg-indigo-500 selection:text-white">
        <div class="min-h-screen bg-slate-50 dark:bg-slate-950 transition-colors duration-500">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="glass-effect shadow-sm border-b border-white/20 dark:border-white/10 mt-8 mx-4 rounded-2xl relative z-10">
                    <div class="max-w-7xl mx-auto py-5 px-6 sm:px-8 lg:px-10">
                        <div class="heading-font">
                            {{ $header }}
                        </div>
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="animate-fade-in px-4 py-4">
                {{ $slot }}
            </main>
        </div>

        <!-- SweetAlert2 popup script (Global) -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                @if(session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: "{!! addslashes(session('success')) !!}",
                        confirmButtonColor: '#4f46e5',
                        timer: 3000,
                        timerProgressBar: true
                    });
                @endif

                @if(session('error'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: "{!! addslashes(session('error')) !!}",
                        confirmButtonColor: '#e11d48'
                    });
                @endif
                
                @if($errors->any() && !request()->routeIs('*.create') && !request()->routeIs('*.edit'))
                    // Only show global error alert if we are not on a form page, 
                    // since form pages have their own specific SweetAlert logic for $errors to format them as a list.
                @endif
            });
        </script>
    </body>
</html>
