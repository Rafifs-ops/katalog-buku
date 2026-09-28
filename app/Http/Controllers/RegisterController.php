<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Register a new user and authenticate them.
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = User::create($request->validated());

        Auth::guard('web')->login($user);

        $request->session()->regenerate();

        return redirect()->route('admin');
    }
}
