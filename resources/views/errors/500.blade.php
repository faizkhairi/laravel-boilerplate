<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 - Server Error</title>
    @vite(['resources/css/app.css'])
</head>
<body class="h-full">
    <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 text-center">
            <!-- Error Code -->
            <div>
                <h1 class="text-9xl font-extrabold text-red-600 dark:text-red-500 tracking-tight">
                    500
                </h1>
                <p class="mt-4 text-3xl font-bold text-gray-700 dark:text-gray-300">
                    Server Error
                </p>
                <p class="mt-2 text-base text-gray-600 dark:text-gray-400">
                    Oops! Something went wrong on our end. We're working to fix it.
                </p>
            </div>

            <!-- Error Icon -->
            <div class="flex justify-center">
                <svg class="h-16 w-16 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>

            <!-- Actions -->
            <div class="mt-8 space-y-4">
                <button
                    onclick="window.location.reload()"
                    class="inline-flex items-center justify-center w-full px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gray-900 hover:bg-gray-800 dark:bg-gray-50 dark:text-gray-900 dark:hover:bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900"
                >
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Try Again
                </button>

                <a
                    href="{{ url('/') }}"
                    class="inline-flex items-center justify-center w-full px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                >
                    Go to Homepage
                </a>
            </div>

            <!-- Help Text -->
            <div class="mt-8 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    If this problem persists, please
                    <a href="mailto:support@example.com" class="font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white underline">
                        contact our support team
                    </a>
                    with the error details.
                </p>
                @if(config('app.debug') && isset($exception))
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-500 font-mono break-all">
                        Error: {{ $exception->getMessage() }}
                    </p>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
