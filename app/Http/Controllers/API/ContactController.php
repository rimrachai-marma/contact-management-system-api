<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller {
    // route::get -> /contacts
    // List all contacts for the authenticated user
    // Supports search, started filter, and sorting
    // Search by first name, last name, or email
    // Started filter: 0 (not started) or 1 (started)
    // Sorting by name (first name) or created_at date
    public function index(Request $request) {
        $query = Contact::where('user_id', Auth::id());

        // Search
         if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($_query) use ($search) {
                $_query->where('first_name', 'like', '%' . $search . '%')
                ->orWhere('last_name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Started filter
        if ($request->has('started') && in_array($request->started, ['0', '1'])) {
            $query->where('started', $request->started);
        }

        // Sorting
        $sortKey = $request->input('sort', 'name');
        $order = $request->input('order', 'asc');
 
        $sort = ['name' => 'first_name', 'created_at' => 'created_at'][$sortKey] ?? 'first_name';
        $order = in_array(strtolower($order), ['asc', 'desc']) ? strtolower($order) : 'asc';

        $contacts = $query->orderBy($sort, $order)->paginate(12);
        $totalContacts = Contact::where('user_id', Auth::id())->count();

         return response()->json([
            'status' => 'success',
            'data'=> [
                'contacts' => $contacts->items(),
                'totalContacts' => $totalContacts,
                'pagination' => [
                    'page' => $contacts->currentPage(),
                    'pages' => $contacts->lastPage(),
                    'per_page' => $contacts->perPage(),
                    'total' => $contacts->total(),
                ],
            ],
        ]);
    }

    // route::post -> /contacts
    // Create a new contact
    public function store(StoreContactRequest $request) {
        $data = array_merge($request->validated(), ['user_id' => Auth::id()]);
        $contact = Contact::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Contact created!',
            'data' => [
                'contact' => $contact,
            ]
        ], 201);
        
    }

    // route::get -> /contacts/{id}
    // Show a specific contact
    // Only the owner can view their contact
    public function show(Contact $contact) {
        if ($contact->user_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Access denied, Forbidden',
            ],403);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'contact' => $contact,
            ]
        ], 200);
    }


    // route::patch -> /contacts/{id}
    // Update a specific contact
    // Only the owner can update their contact
    public function update(UpdateContactRequest $request, Contact $contact) {
        if ($contact->user_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Access denied, Forbidden',
            ], 403);
        }

        $contact->update($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Contact updated successfully!',
            'data' => [
                'contact' => $contact,
            ]
        ], 200);
    }

    // route::delete -> /contacts/{id}
    // Delete a specific contact
    // Only the owner can delete their contact
    public function destroy(Contact $contact) {
        if ($contact->user_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Access denied, Forbidden',
            ], 403);
        }

        $contact->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Contact deleted successfully!',
            'data' => [
                'contact' => $contact,
            ]
        ], 200);
    }

    // route::patch -> /contacts/{id}/toggle-started
    // Toggle the "started" status of a contact
    // Only the owner can toggle their contact's status
    public function toggleStarted(Contact $contact) {
        if ($contact->user_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Access denied, Forbidden',
            ], 403);
        }

        $contact->started = !$contact->started;
        $contact->save();

        return response()->json([
            'status' => 'success',
            'message' => $contact->started ? 'Contact starred!' : 'Contact unstarred!',
            'data' => [
                'contact' => $contact,
            ]
        ], 200);
    }
    
}
