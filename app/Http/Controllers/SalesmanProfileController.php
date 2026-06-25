<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;

class SalesmanProfileController extends Controller
{
    public function edit()
    {
        if (!Auth::guard('salesman')->check()) {
            abort(403);
        }
        $salesman = Auth::guard('salesman')->user();
        return view('salesman.profile', compact('salesman'));
    }

    public function update(Request $request)
    {
        if (!Auth::guard('salesman')->check()) {
            abort(403);
        }

        $salesman = Auth::guard('salesman')->user();

        if ($request->filled('name')) {
            $baseUsername = \Illuminate\Support\Str::of($request->name)->lower()->replace(' ', '');
            $username = (string) $baseUsername;
            $count = 1;

            while (
                \App\Models\Manager::where('username', $username)->exists()
                || \App\Models\Salesman::where('username', $username)->where('salesman_id', '!=', $salesman->salesman_id)->exists()
            ) {
                $username = $baseUsername . $count;
                $count++;
            }

            $request->merge(['username' => $username]);
        }

        $rules = [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique('manager', 'email'),
                Rule::unique('salesman', 'email')->ignore($salesman->salesman_id, 'salesman_id'),
            ],
            'address'  => ['required', 'string', 'max:500'],
            'phone_number' => ['required', 'regex:/^[0-9]{3}-[0-9]{7,8}$/'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];

        if ($request->filled('password')) {
            $rules['password'] = [
                'required', 'string', 'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&]/',
                'confirmed',
            ];
        }

        $request->validate($rules, [
            'phone_number.regex' => 'Invalid phone format. Use 012-3456789 or 012-34567890',
        ]);

        $data = [
            'name'           => $request->name,
            'username'       => $request->username,
            'email'          => $request->email,
            'address'        => $request->address,
            'phone_number'   => $request->phone_number,
        ];

        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($salesman->profile_picture && \Illuminate\Support\Facades\Storage::disk('public')->exists($salesman->profile_picture)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($salesman->profile_picture);
            }

            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $data['profile_picture'] = $path;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $salesman->update($data);

        return redirect()->route('salesman.profile.edit')->with('success', 'Profile updated successfully.');
    }

    public function destroy(Request $request)
    {
        if (!Auth::guard('salesman')->check()) {
            abort(403);
        }

        $salesman = Auth::guard('salesman')->user();
        Auth::guard('salesman')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $salesman->delete();

        return redirect('/')->with('success', 'Your account has been deleted.');
    }
}
