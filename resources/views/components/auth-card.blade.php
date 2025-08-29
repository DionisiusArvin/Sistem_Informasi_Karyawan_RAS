<div class="min-h-screen flex items-center justify-center 
            bg-gray-100 dark:bg-gray-900 transition-colors duration-500">

    <div class="w-full max-w-md bg-white/80 dark:bg-gray-800/70 
                backdrop-blur-xl shadow-xl rounded-2xl p-8">

        <!-- Judul -->
        @isset($title)
            <h2 class="text-2xl font-bold text-center 
                       text-gray-800 dark:text-gray-100 mb-6">
                {{ $title }}
            </h2>
        @endisset

        <!-- Slot untuk form -->
        {{ $slot }}
    </div>
</div>
