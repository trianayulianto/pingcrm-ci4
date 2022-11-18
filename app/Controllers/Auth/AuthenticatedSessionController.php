<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;
use Inertia\Inertia;

class AuthenticatedSessionController extends BaseController
{
    /** @var \App\Models\UserModel $userModel */
    protected $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function create()
    {
        return Inertia::render('Auth/Login');
    }

    public function store()
    {
        // Validate this credentials request.
        if (! $this->validate(['email' => 'required|valid_email', 'password' => 'required'])) {
            return redirect()->back()->withInput();
        }

        $credentials = [
            'email' => $this->request->getVar('email'),
            'password' => $this->request->getVar('password'),
        ];

        $user = $this->userModel->where('email', $credentials['email'])->first();

        if (! $user || ! password_verify($credentials['password'], $user->password)) {
            $this->validator->setError('email', 'Credentials does not match.');

            return redirect()->back()->withInput();
        }

        service('auth')->login($user->id);

        return Inertia::location('/');
    }

    public function destroy()
    {
        service('auth')->logout();

        return redirect('/');
    }
}
