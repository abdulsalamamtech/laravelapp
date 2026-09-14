<?php

namespace App\Http\Controllers\V1\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\Contact;

class AdminContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contactMessage = Contact::latest()->get();
        if ($contactMessage->isEmpty()) {
            return ApiResponse::success([], 'No contact messages found', 404);
        }

        return ApiResponse::success($contactMessage, 'Contact messages retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactRequest $request)
    {
        $validatedData = $request->validated();

        $contactMessage = Contact::create($validatedData);

        return ApiResponse::success($contactMessage, 'Contact message created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        return ApiResponse::success($contact, 'Contact message retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContactRequest $request, Contact $contact)
    {
        $data = $request->validated();
        $contact->update($data);

        return ApiResponse::success($contact, 'Contact message updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return ApiResponse::success([], 'Contact message deleted successfully', 200);
    }
}
