<x-guest-layout>
    <div x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }" 
         x-init="$watch('darkMode', val => { 
            document.documentElement.classList.toggle('dark', val); 
            localStorage.setItem('theme', val ? 'dark' : 'light'); 
         })"
         :class="{ 'dark': darkMode }">

            <!-- Toggle Dark Mode -->
            <button @click="darkMode = !darkMode"
                class="absolute top-5 right-5 p-2 rounded-full shadow-md 
                       bg-white dark:bg-gray-700 
                       hover:scale-110 transition transform">
                <template x-if="!darkMode">
                    <svg class="w-6 h-6 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 3.22l.61 1.25a1 1 0 00.75.55l1.38.2a1 1 0 01.55 1.7l-1 1a1 1 0 00-.29.88l.23 1.38a1 1 0 01-1.45 1.05l-1.24-.65a1 1 0 00-.94 0l-1.24.65a1 1 0 01-1.45-1.05l.23-1.38a1 1 0 00-.29-.88l-1-1a1 1 0 01.55-1.7l1.38-.2a1 1 0 00.75-.55L10 3.22z"/>
                    </svg>
                </template>
                <template x-if="darkMode">
                    <svg class="w-6 h-6 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                    </svg>
                </template>
            </button>

            <div class="w-full max-w-md p-8 space-y-6 
                        bg-white/80 dark:bg-gray-800/70 
                        backdrop-blur-xl rounded-2xl shadow-2xl border 
                        border-gray-100 dark:border-gray-700 
                        transition-colors duration-500">
                
                <!-- Logo -->
                <div class="text-center">
                    <a href="/" class="inline-block">
                        <svg class="h-12 w-12 text-blue-600 dark:text-blue-400 mx-auto" fill="none" viewBox="0 0 54 54">
                            <path d="M27 54C12.1125 54 0 41.8875 0 27C0 12.1125 12.1125 0 27 0C41.8875 0 54 12.1125 54 27C54 41.8875 41.8875 54 27 54ZM27 3C13.785 3 3 13.785 3 27C3 40.215 13.785 51 27 51C40.215 51 51 40.215 51 27C51 13.785 40.215 3 27 3Z" fill="currentColor"/>
                            <path d="M29.25 13.5H24.75V24.75H13.5V29.25H24.75V40.5H29.25V29.25H40.5V24.75H29.25V13.5Z" fill="currentColor"/>
                        </svg>
                    </a>
                    <h2 class="mt-4 text-3xl font-extrabold text-gray-900 dark:text-white">
                        Selamat Datang 👋
                    </h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Masuk untuk melanjutkan ke dashboard Anda
                    </p>
                </div>

                <!-- Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <!-- Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Email / Username -->
                    <div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M16 12a4 4 0 10-8 0 4 4 0 008 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M12 14v7m0 0h7m-7 0H5" />
                                </svg>
                            </span>
                            <x-text-input id="login" type="text" name="login" :value="old('login')" 
                                required autofocus 
                                placeholder="Email atau Nama" 
                                class="pl-10 w-full rounded-xl dark:bg-gray-700 dark:text-white dark:placeholder-gray-400" />
                        </div>
                        <x-input-error :messages="$errors->get('login')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <x-input-label for="password" value="Password" class="dark:text-gray-200" />
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:underline dark:text-blue-400">
                                    Lupa password?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                            <x-text-input id="password" type="password" name="password" required 
                                placeholder="••••••••" 
                                class="pl-10 w-full rounded-xl dark:bg-gray-700 dark:text-white dark:placeholder-gray-400" />
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Button -->
                    <div>
                        <x-primary-button 
                            class="w-full justify-center py-3 mt-2 rounded-xl 
                                   bg-blue-600 hover:bg-blue-700 
                                   dark:bg-blue-500 dark:hover:bg-blue-600 
                                   text-white shadow-lg hover:shadow-xl 
                                   transform hover:scale-[1.02] transition">
                            {{ __('Log In') }}
                        </x-primary-button>
                    </div>
                </form>

                <!-- Register -->
                <p class="mt-6 text-sm text-center text-gray-500 dark:text-gray-400">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="font-medium text-blue-600 dark:text-blue-400 hover:underline">
                        Daftar di sini
                    </a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
