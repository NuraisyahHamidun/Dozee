<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Salesmen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function index()
    {
        if (Auth::guard('manager')->check()) {
            $salesmen = Auth::guard('manager')->user()->salesmen()->latest()->get();
            return view('accounts.index', compact('salesmen'));
        }
        
        abort(403, 'Unauthorized action.');
    }

    public function create()
    {
        if (!Auth::guard('manager')->check()) {
            abort(403);
        }
        
        return view('accounts.create');
    }

    public function store(Request $request)
    {
        if (!Auth::guard('manager')->check()) {
            abort(403);
        }

        if ($request->filled('name')) {
            $baseUsername = \Illuminate\Support\Str::of($request->name)->lower()->replace(' ', '');
            $username = $baseUsername;
            $count = 1;

            while (\App\Models\Manager::where('username', $username)->exists() || \App\Models\Salesmen::where('username', $username)->exists()) {
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
                Rule::unique('salesmen', 'username'),
            ],
            'email'    => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique('manager', 'email'),
                Rule::unique('salesmen', 'email'),
            ],
            'password' => [
                'required', 'string', 'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&]/',
                'confirmed',
            ],
        ]);

        $manager = Auth::guard('manager')->user();

        $salesmen = $manager->salesmen()->create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'staff_code' => Salesmen::generateUniqueStaffCode(),
        ]);

        return redirect()->route('accounts.index')->with('success', 'Salesmen account created successfully.');
    }

    public function edit($id)
    {
        if (!Auth::guard('manager')->check()) {
            abort(403);
        }

        $manager = Auth::guard('manager')->user();
        $salesmen = $manager->salesmen()->findOrFail($id);

        return view('accounts.edit', compact('salesmen'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::guard('manager')->check()) {
            abort(403, 'Unauthorized');
        }

        $manager = Auth::guard('manager')->user();
        $salesmen = $manager->salesmen()->findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required', 'string', 'lowercase', 'max:255',
                Rule::unique('manager', 'username'),
                Rule::unique('salesmen', 'username')->ignore($salesmen->salesmen_id, 'salesmen_id'),
            ],
        ]);

        $salesmen->update([
            'name' => $request->name,
            'username' => $request->username,
        ]);

        return redirect()->back()->with('success', 'Salesmen profile updated successfully.');
    }

    public function destroy($id)
    {
        if (!Auth::guard('manager')->check()) {
            abort(403);
        }

        $manager = Auth::guard('manager')->user();
        $salesmen = $manager->salesmen()->findOrFail($id);
        $salesmen->delete();

        return redirect()->route('accounts.index')->with('success', 'Salesmen account deleted successfully.');
    }
}
