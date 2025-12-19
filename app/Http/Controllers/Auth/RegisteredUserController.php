<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\RegisterRequest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class RegisteredUserController extends Controller
{
    // GET /register
    public function create()
    {
        return view('auth.register');
    }

    // POST /register
    public function store(RegisterRequest $request, CreatesNewUsers $creator)
    {
        $user = $creator->create($request->validated());

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verify.notice.custom');
    }
}
