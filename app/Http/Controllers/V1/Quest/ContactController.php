<?php

namespace App\Http\Controllers\V1\Quest;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Mail\ContactMail;
use App\Models\Contact;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactRequest $request)
    {
        $validatedData = $request->validated();

        $contactMessage = Contact::create($validatedData);
        if (! $contactMessage) {
            return ApiResponse::error([], 'Something went wrong, please try again later!', 403);
        }

        $admin_mail = config('mail.data.admin');
        Mail::to($admin_mail)->send(new ContactMail($contactMessage));
        Log::info('Contact created successful', [$contactMessage]);

        return ApiResponse::success([], 'Message sent successfully, you will receive a response shortly!', 201);
    }
}
