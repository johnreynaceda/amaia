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
    @wireUiScripts
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased relative" x-data="app()">
    <div class="fixed bg-gray-800 top-0 left-0 bottom-0  right-0">
        <img src="{{ asset('images/page_bg.jpg') }}" class="h-full w-full opacity-50 object-cover" alt="">
    </div>
    <div>
        <div class="p-5 relative flex justify-center items-center 2xl:hidden">
            <img src="{{ asset('images/amaia_logo.png') }}" class="xl:h-24 h-20 relative z-10" alt="">
        </div>

        <div class="p-5 relative  2xl:block hidden">
            <img src="{{ asset('images/amaia_logo.png') }}" class="xl:h-24 h-20 relative z-10" alt="">
        </div>
        <section>
            <div class="2xl:py-24 py-10 bg-white">
                <div class="relative px-8">
                    <div class="max-w-3xl text-center lg:text-left">
                        <div class="max-w-xl mx-auto text-center lg:p-10 lg:text-left">
                            <div>
                                <p class="text-2xl font-black tracking-tight text-white sm:text-4xl">
                                    Hello,
                                </p>
                                <p class="text-2xl font-black tracking-tight text-white sm:text-5xl">
                                    Welcome Back!
                                </p>

                            </div>

                            <div
                                class="flex flex-col items-center mb-5 justify-center gap-3 mt-10 2xl:mt-16 lg:flex-row lg:justify-start">
                                <p class="text-gray-700">Please enter your Amaia account.</p>

                            </div>
                            <div class="flex flex-col space-y-5">
                                <x-input class="2xl:h-12 " icon="user" placeholder="Email address" />
                                <x-inputs.password class="2xl:h-12 " icon="key" placeholder="Password" />
                                <x-button label="SIGN IN" dark class="font-semibold" right-icon="login" md />
                                <div class="relative">
                                    <div class="absolute inset-0 flex items-center">
                                        <div class="w-full border-t-2 border-gray-300"></div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm 2xl:text-md">Don't Have an Account? <a href=""
                                            class="text-white hover:text-cyan-500 2xl:hover:text-main">Register
                                            here.</a></span>
                                </div>

                                <div class="text-left">
                                    <span class="text-sm text-gray-200 2xl:text-md">Visiting? Please select and Fill up
                                        the form
                                        provided.</span>
                                    <livewire:pass-user-request />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

</body>

</html>
