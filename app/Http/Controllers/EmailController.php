<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomEmail;

class EmailController extends Controller
{
    public function sendEmail(Request $request) {

        $defaultEmail = 'example@example.com';

        // Validate the request
        $request->validate([
            'subject' => 'required|string|min:5|max:255',
            'message' => 'required|string',
            'file_upload' => 'nullable|file|max:10240'   // Max file size 10mb
        ]);

        // Get any uploaded files
        $attachment = $request->file('file_upload');

        // Send the email
        Mail::to($defaultEmail)->send(new CustomEmail(
            $request->subject,
            $request->message,
            $attachment
        ));

        return back()->with('success', 'Email sent!');
    }
}
