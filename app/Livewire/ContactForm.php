<?php

namespace App\Livewire;

use App\Models\ContactSubmission;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ContactForm extends Component
{
    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('required|email|max:255')]
    public $email = '';

    #[Validate('required|string|max:255')]
    public $subject = '';

    #[Validate('required|string|max:5000')]
    public $message = '';

    // Honeypot field for spam protection
    public $website = '';

    public $success = false;

    public function submit()
    {
        // Check honeypot - if filled, it's likely spam
        if (!empty($this->website)) {
            return;
        }

        $this->validate();

        // Create the contact submission
        $submission = ContactSubmission::create([
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
            'meta' => [
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ],
        ]);

        // Send email notification
        try {
            Mail::to(env('MAIL_TARGET'))->send(new \App\Mail\ContactSubmitted($submission));
        } catch (\Exception $e) {
            // Log the error but don't fail the submission
            \Log::error('Failed to send contact form email: ' . $e->getMessage());
        }

        // Reset form and show success
        $this->reset(['name', 'email', 'subject', 'message', 'website']);
        $this->success = true;
    }

    public function render()
    {
        return view('livewire.contact-form')
            ->layout('layouts.app');
    }
}
