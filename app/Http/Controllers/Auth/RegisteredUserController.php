<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Division; // 1. Tambahkan ini untuk menggunakan model Division
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // 2. Ambil semua data divisi dan kirim ke view
        $divisions = Division::all();
        return view('auth.register', ['divisions' => $divisions]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 3. Tambahkan validasi untuk division_id
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'division_id' => ['required', 'exists:divisions,id'], // Validasi untuk divisi
        ]);

        // 4. Tambahkan division_id dan role 'staff' saat membuat user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'division_id' => $request->division_id,
            'role' => 'staff', // Langsung set role sebagai staff
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect('/dashboard');
    }
}