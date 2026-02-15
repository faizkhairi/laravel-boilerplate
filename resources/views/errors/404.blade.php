<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Page Not Found</title>
    @vite(['resources/css/app.css'])
</head>
<body class="h-full">
    <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 text-center">
            <!-- Error Code -->
            <div>
                <h1 class="text-9xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                    404
                </h1>
                <p class="mt-4 text-3xl font-bold text-gray-700 dark:text-gray-300">
                    Page Not Found
                </p>
                <p class="mt-2 text-base text-gray-600 dark:text-gray-400">
                    Sorry, we couldn't find the page you're looking for.
                </p>
            </div>

            <!-- Actions -->
            <div class="mt-8 space-y-4">
                <a
                    href="{{ url('/') }}"
                    class="inline-flex items-center justify-center w-full px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gray-900 hover:bg-gray-800 dark:bg-gray-50 dark:text-gray-900 dark:hover:bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900"
                >
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Go to Homepage
                </a>

                @auth
                    <a
                        href="{{ route('dashboard') }}"
                        class="inline-flex items-center justify-center w-full px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                    >
                        Go to Dashboard
                    </a>
                @else
                    <a
                        href="{{ route('login') }}"
                        class="inline-flex items-center justify-center w-full px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                    >
                        Sign In
                    </a>
                @endauth
            </div>

            <!-- Help Text -->
            <p class="mt-8 text-sm text-gray-500 dark:text-gray-400">
                If you think this is a mistake, please
                <a href="mailto:support@example.com" class="font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white underline">
                    contact support
                </a>.
            </p>
        </div>
    </div>
</body>
</html>
