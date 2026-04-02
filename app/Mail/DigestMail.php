<?php

namespace App\Mail;

use App\Models\Digest;
use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DigestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Digest $digest,
        public readonly Subscriber $subscriber,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->digest->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.digest',
        );
    }
}
