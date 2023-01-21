<?php

namespace App\Controllers;

use App\Models\OrganizationModel;
use Inertia\Inertia;

class OrganizationsController extends BaseController
{
    protected $organizationModel;

    public function __construct() {
        $this->organizationModel = new OrganizationModel();
    }

    public function index()
    {
        $organizations = $this->organizationModel->findByParams(
            $this->request->getVar('search'),
            $this->request->getVar('trashed')
        );

        return Inertia::render('Organizations/Index', [
            'filters' => $this->request->getVar(['search', 'trashed']),
            'organizations' => [
                'data' => array_values(
                    array_map(fn ($organization) => [
                        'id' => $organization->id,
                        'name' => $organization->name,
                        'phone' => $organization->phone,
                        'city' => $organization->city,
                        'deleted_at' => $organization->deleted_at,
                    ], $organizations['data'])
                ),
                'links' => $organizations['links'],
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Organizations/Create');
    }

    public function store()
    {
        $validator = $this->validate([
            'name' => ['required', 'max_length[100]'],
            'email' => ['permit_empty', 'max_length[50]', 'valid_email'],
            'phone' => ['permit_empty', 'max_length[50]'],
            'address' => ['permit_empty', 'max_length[150]'],
            'city' => ['permit_empty', 'max_length[50]'],
            'region' => ['permit_empty', 'max_length[50]'],
            'country' => ['permit_empty', 'max_length[2]'],
            'postal_code' => ['permit_empty', 'max_length[25]'],
        ]);

        if (! $validator) {
            return redirect()->back()->withInput();
        }

        $authShared = Inertia::getShared('auth');

        $data = $this->request->getVar([
            'name', 'email', 'phone', 'address', 'city', 'region', 'country', 'postal_code',
        ]);

        $this->userModel->insert(
            array_merge([
                'account_id' => $authShared['user']['account']['id'] ?? null,
            ], $data)
        );

        return redirect()->to('/organizations')->with('success', 'Organization created.');
    }

    public function edit($id)
    {
        if (! $organization = $this->organizationModel->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return Inertia::render('Organizations/Edit', [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'email' => $organization->email,
                'phone' => $organization->phone,
                'address' => $organization->address,
                'city' => $organization->city,
                'region' => $organization->region,
                'country' => $organization->country,
                'postal_code' => $organization->postal_code,
                'deleted_at' => $organization->deleted_at,
                'contacts' => array_map(fn ($contact) => [
                    'id' => $contact->id,
                    'name' => $contact->name,
                    'city' => $contact->city,
                    'phone' => $contact->phone,
                ], $organization->contacts ?? []),
            ],
        ]);
    }

    public function update($id)
    {
        if (! $organization = $this->organizationModel->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $validator = $this->validate([
            'name' => ['required', 'max_length[100]'],
            'email' => ['permit_empty', 'max_length[50]', 'valid_email'],
            'phone' => ['permit_empty', 'max_length[50]'],
            'address' => ['permit_empty', 'max_length[150]'],
            'city' => ['permit_empty', 'max_length[50]'],
            'region' => ['permit_empty', 'max_length[50]'],
            'country' => ['permit_empty', 'max_length[2]'],
            'postal_code' => ['permit_empty', 'max_length[25]'],
        ]);

        if (! $validator) {
            return redirect()->back()->withInput();
        }

        $organization->fill($this->request->getVar([
            'name', 'email', 'phone', 'address', 'city', 'region', 'country', 'postal_code',
        ]));

        try {
            $this->organizationModel->save($organization);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }

        return redirect()->back()->with('success', 'Organization updated.');
    }

    public function destroy($id)
    {
        if (! $organization = $this->organizationModel->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $organization->delete();

        return redirect()->to('/contacts')->with('success', 'Organization deleted.');
    }

    public function restore($id)
    {
        if (! $organization = $this->organizationModel->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $organization->fill(['deleted_at' => null]);

        $this->organizationModel->save($organization);

        return redirect()->back()->with('success', 'Organization restored.');
    }
}
