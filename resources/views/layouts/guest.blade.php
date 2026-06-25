<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Dozee') }} | Authentication</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,900&family=outfit:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            .mesh-bg {
                background-color: #ffffff;
                background-image: 
                    radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.1) 0px, transparent 50%),
                    radial-gradient(at 100% 0%, rgba(168, 85, 247, 0.1) 0px, transparent 50%),
                    radial-gradient(at 100% 100%, rgba(236, 72, 153, 0.05) 0px, transparent 50%);
            }
        </style>
    </head>
    <body class="font-sans text-slate-900 antialiased mesh-bg min-h-screen flex items-center justify-center p-6">
        <div class="w-full sm:max-w-md">
            <div class="flex justify-center mb-8">
                <a href="/" class="flex items-center gap-3 group">
                    <span class="p-2.5 bg-indigo-600 rounded-2xl text-white shadow-xl shadow-indigo-200 group-hover:scale-110 transition-transform duration-300">
                         <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </span>
                    <span class="heading-font font-black text-2xl tracking-tight text-slate-900 uppercase">Do'zee</span>
                </a>
            </div>

            <div class="glass-effect p-10 rounded-[40px] border border-white/40 shadow-2xl shadow-indigo-100/50">
                {{ $slot }}
            </div>
            
            <div class="mt-8 text-center">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Secure Login System</p>
            </div>
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

                @if(session('error') || session('status'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: "{!! addslashes(session('error') ?? session('status')) !!}",
                        confirmButtonColor: '#e11d48'
                    });
                @endif
                
                @if($errors->any())
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        html: `<ul style="list-style: none; padding: 0;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                               </ul>`,
                        confirmButtonColor: '#e11d48'
                    });
                @endif
            });
        </script>
    </body>
</html>
