<?php

namespace App\Controllers;

use App\Models\Contact;
use App\Models\ContactModel;
use App\Models\OrganizationModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ContactsController extends BaseController
{
    protected $contactModel;
    protected $organizationModel;

    public function __construct() {
        $this->contactModel = new ContactModel();
        $this->organizationModel = new OrganizationModel();
    }

    public function index()
    {
        $contacts = $this->contactModel->findByParams(
            $this->request->getVar('search'),
            $this->request->getVar('trashed')
        );

        return Inertia::render('Contacts/Index', [
            'filters' => $this->request->getVar(['search', 'trashed']),
            'contacts' => [
                'data' => array_values(
                    array_map(fn ($contact) => [
                        'id' => $contact->id,
                        'name' => $contact->name,
                        'phone' => $contact->phone,
                        'city' => $contact->city,
                        'deleted_at' => $contact->deleted_at,
                        'organization' => $contact->organization ? ['name' => $contact->organization->name] : null,
                    ], $contacts['data'])
                ),
                'links' => $contacts['links'],
            ],
        ]);
    }

    public function create()
    {
        helper('auth');

        return Inertia::render('Contacts/Create', [
            'organizations' => array_values($this->organizationModel->findByUserId(user_id())),
        ]);
    }

    public function store()
    {
        $validator = $this->validate([
            'first_name' => ['required', 'max_length[50]'],
            'last_name' => ['required', 'max_length[50]'],
            'organization_id' => ['permit_empty'],
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
            'first_name', 'last_name', 'organization_id', 'email', 'phone', 'address', 'city', 'region', 'country', 'postal_code',
        ]);

        $this->userModel->insert(
            array_merge([
                'account_id' => $authShared['user']['account']['id'] ?? null,
            ], $data)
        );

        return redirect()->route('/contacts')->with('success', 'Contact created.');
    }

    public function edit($id)
    {
        if (! $contact = $this->contactModel->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        helper('auth');

        return Inertia::render('Contacts/Edit', [
            'contact' => [
                'id' => $contact->id,
                'first_name' => $contact->first_name,
                'last_name' => $contact->last_name,
                'organization_id' => $contact->organization_id,
                'email' => $contact->email,
                'phone' => $contact->phone,
                'address' => $contact->address,
                'city' => $contact->city,
                'region' => $contact->region,
                'country' => $contact->country,
                'postal_code' => $contact->postal_code,
                'deleted_at' => $contact->deleted_at,
            ],
            'organizations' => array_values($this->organizationModel->findByUserId(user_id())),
        ]);
    }

    public function update($id)
    {
        if (! $contact = $this->contactModel->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $validator = $this->validate([
            'first_name' => ['required', 'max_length[50]'],
            'last_name' => ['required', 'max_length[50]'],
            'organization_id' => ['permit_empty'],
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
            'first_name', 'last_name', 'organization_id', 'email', 'phone', 'address', 'city', 'region', 'country', 'postal_code',
        ]);

        $contact->fill(
            array_merge([
                'account_id' => $authShared['user']['account']['id'] ?? null,
            ], $data)
        );

        try {
            $this->contactModel->save($contact);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }

        return redirect()->back()->with('success', 'Contact updated.');
    }

    public function destroy($id)
    {
        if (! $contact = $this->contactModel->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $contact->delete();

        return redirect()->back()->with('success', 'Contact deleted.');
    }

    public function restore($id)
    {
        if (! $contact = $this->contactModel->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $contact->fill(['deleted_at' => null]);

        $this->contactModel->save($contact);

        return redirect()->back()->with('success', 'Contact restored.');
    }
}
