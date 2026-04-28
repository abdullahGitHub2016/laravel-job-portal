<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function createSeeker(): Response
    {
        return Inertia::render('Auth/Register', ['userType' => 'job_seeker']);
    }

    public function createEmployer(): Response
    {
        return Inertia::render('Auth/Register', ['userType' => 'employer']);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'  => ['required', 'confirmed', Rules\Password::defaults()],
            'user_type' => ['required', 'in:job_seeker,employer'],
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'user_type' => $request->user_type,
        ]);

        event(new Registered($user));
        Auth::login($user);

        if ($user->isEmployer()) {
            return redirect()->route('employer.company.edit');
        }

        return redirect()->route('seeker.profile.edit');
    }
}
