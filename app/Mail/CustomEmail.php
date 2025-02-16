<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class CustomEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $messageBody;
    public $attachment;

    /**
     * Create a new message instance.
     */
    public function __construct($subject, $message, $attachment = null)
    {
        $this->subject = $subject;
        $this->messageBody = $message;
        $this->attachment = $attachment;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {

        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.custom-email',
            with: [
                'user' => Auth::user()->name,
                'userEmail' => Auth::user()->email,
                'subject' => $this->subject,
                'messageBody' => $this->messageBody,
                //'attachment' => $this->attachment
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        if($this->attachment) {
            $attachments[] = Attachment::fromPath($this->attachment->getPathname())
                ->as($this->attachment->getClientOriginalName())
                ->withMime($this->attachment->getMimeType());
        }
        return $attachments;
    }
}
