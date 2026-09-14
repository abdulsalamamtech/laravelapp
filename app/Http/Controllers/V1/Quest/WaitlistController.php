<?php

namespace App\Http\Controllers\V1\Quest;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Quest\StoreWaitlistRequest;
use App\Http\Requests\Quest\UpdateWaitlistRequest;
use App\Mail\WaitlistMail;
use App\Models\Waitlist;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WaitlistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWaitlistRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['ip_address'] = $request?->ip();

        $waitlist = Waitlist::create($validatedData);
        if (! $waitlist) {
            return ApiResponse::error([], 'Something went wrong, please try again later!', 403);
        }

        $admin_mail = config('mail.data.admin');
        Mail::to($admin_mail)->send(new WaitlistMail($waitlist));
        Log::info('Waitlist created successful', [$waitlist]);

        return ApiResponse::success([], 'You have been added to the waitlist!', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Waitlist $waitlist)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Waitlist $waitlist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWaitlistRequest $request, Waitlist $waitlist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Waitlist $waitlist)
    {
        //
    }
}
