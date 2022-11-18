<?php

namespace App\Controllers;

use App\Models\UserModel;
use Inertia\Inertia;

class UsersController extends BaseController
{
    protected $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        return Inertia::render('Users/Index', [
            'filters' => $this->request->getVar(['search', 'role', 'trashed']),
            'users' => array_values(
                array_map(fn ($user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'owner' => $user->owner,
                    'photo' => $user->photo_path,
                    'deleted_at' => $user->deleted_at,
                ], $this->userModel->findByParams(
                    $this->request->getVar('search'),
                    $this->request->getVar('role'),
                    $this->request->getVar('trashed')
                ))
            ),
        ]);
    }

    public function create()
    {
        return Inertia::render('Users/Create');
    }

    public function store()
    {
        $validator = $this->validate([
            'first_name' => ['required', 'max_length[50]'],
            'last_name' => ['required', 'max_length[50]'],
            'email' => ['required', 'max_length[50]', 'valid_email', 'is_unique[users.email]'],
            'password' => ['permit_empty'],
            'owner' => ['permit_empty'],
            'photo' => ['permit_empty', 'image'],
        ]);

        if (! $validator) {
            return redirect()->back()->withInput();
        }

        $authShared = Inertia::getShared('auth');

        $this->userModel->insert([
            'account_id' => $authShared['user']['account']['id'] ?? null,
            'first_name' => $this->request->getVar('first_name'),
            'last_name' => $this->request->getVar('last_name'),
            'email' => $this->request->getVar('email'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_BCRYPT),
            'owner' => $this->request->getVar('owner'),
        ]);

        return redirect()->to('/users')->with('success', 'User created.');
    }

    public function edit($id)
    {
        if (! $user = $this->userModel->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return Inertia::render('Users/Edit', [
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'owner' => $user->owner,
                'photo' => $user->photo_path,
                'deleted_at' => $user->deleted_at,
            ],
        ]);
    }

    public function update($id)
    {
        if (! $user = $this->userModel->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $validator = $this->validate([
            'first_name' => ['required', 'max_length[50]'],
            'last_name' => ['required', 'max_length[50]'],
            'email' => ['required', 'max_length[50]', 'valid_email', 'is_unique[users.email,id,'.$id.']'],
            'password' => ['permit_empty'],
            'owner' => ['permit_empty'],
            'photo' => ['permit_empty', 'image'],
        ]);

        if (! $validator) {
            return redirect()->back()->withInput();
        }

        $user->fill($this->request->getVar(['first_name', 'last_name', 'email', 'owner']));

        if ($password = $this->request->getVar('password')) {
            $user->setPassword($password);
        }

        $authShared = Inertia::getShared('auth');

        if (isset($authShared['user']['account']['id'])) {
            $user->account_id = $authShared['user']['account']['id'];
        }

        try {
            $this->userModel->save($user);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }

        return redirect()->back()->with('success', 'User updated.');
    }

    public function destroy($id)
    {
        if (! $user = $this->userModel->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $user->delete();

        return redirect()->back()->with('success', 'User deleted.');
    }

    public function restore($id)
    {
        if (! $user = $this->userModel->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $user->fill(['deleted_at' => null]);

        $this->userModel->save($user);

        return redirect()->back()->with('success', 'User restored.');
    }
}
