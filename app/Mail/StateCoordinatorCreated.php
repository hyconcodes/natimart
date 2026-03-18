<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StateCoordinatorCreated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $password
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to NBTI Hub - State Coordinator Account',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.state-coordinator-created',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
