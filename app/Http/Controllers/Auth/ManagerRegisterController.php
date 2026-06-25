<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Manager;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ManagerRegisterController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.manager-register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->filled('name')) {
            $baseUsername = \Illuminate\Support\Str::of($request->name)->lower()->replace(' ', '');
            $username = $baseUsername;
            $count = 1;

            while (\App\Models\Manager::where('username', $username)->exists() || \App\Models\Salesman::where('username', $username)->exists()) {
                $username = $baseUsername . $count;
                $count++;
            }

            $request->merge(['username' => (string) $username]);
        }

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'username' => [
                'required', 'string', 'lowercase', 'max:255',
                Rule::unique('manager', 'username'),
                Rule::unique('salesman', 'username'),
            ],
            'email'    => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique('manager', 'email'),
                Rule::unique('salesman', 'email'),
            ],
            'password' => [
                'required', 'string', 'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&]/',
                'confirmed',
            ],
            'address'  => ['required', 'string', 'max:500'],
            'phone_number' => ['required', 'regex:/^[0-9]{3}-?[0-9]{7,8}$/'],
        ], [
            'phone_number.regex' => 'Invalid phone number format. Only numbers allowed (10–11 digits). Example: 0123456789 or 012-3456789',
        ]);

        $manager = Manager::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'address'  => $request->address,
        ]);

        $phoneDigits = substr(preg_replace('/\D/', '', $manager->phone_number), -4);
        $manager->staff_code = 'STF-' . $phoneDigits . '-' . strtoupper(\Illuminate\Support\Str::random(2));
        $manager->save();

        event(new Registered($manager));

        // Ensure new accounts do not automatically access dashboard until signed in.
        // Auth::guard('manager')->login($manager);

        return redirect()->route('manager.login')->with('success', 'Account created successfully! Please sign in to access the dashboard.');
    }
}
