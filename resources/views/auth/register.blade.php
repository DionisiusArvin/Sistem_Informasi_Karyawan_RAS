<x-guest-layout>
    <x-auth-card title="Register">
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" class="block mt-1 w-full" 
                              type="text" name="name" :value="old('name')" 
                              required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email -->
            <div class="mt-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full"
                              type="email" name="email" :value="old('email')" 
                              required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Division -->
            <div class="mt-4">
                <x-input-label for="division_id" value="Divisi" />
                <select name="division_id" id="division_id"
                        class="block mt-1 w-full border-gray-300 
                               focus:border-indigo-500 focus:ring-indigo-500 
                               rounded-md shadow-sm dark:bg-gray-700 
                               dark:text-gray-100 dark:border-gray-600">
                    <option selected disabled>Pilih Divisi</option>
                    @foreach ($divisions as $division)
                        <option value="{{ $division->id }}" 
                                {{ old('division_id') == $division->id ? 'selected' : '' }}>
                            {{ $division->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('division_id')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full"
                              type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full"
                              type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between mt-6">
                <a href="{{ route('login') }}" 
                   class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                    {{ __('Already registered?') }}
                </a>

                <x-primary-button>
                    {{ __('Register') }}
                </x-primary-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
