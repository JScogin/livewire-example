<?php

namespace Tests\Unit;

use App\Models\ContactSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_contact_submission()
    {
        $submission = ContactSubmission::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => 'This is a test message.',
            'meta' => [
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Test Browser',
            ],
        ]);

        $this->assertInstanceOf(ContactSubmission::class, $submission);
        $this->assertEquals('John Doe', $submission->name);
        $this->assertEquals('john@example.com', $submission->email);
        $this->assertEquals('Test Subject', $submission->subject);
        $this->assertEquals('This is a test message.', $submission->message);
        $this->assertIsArray($submission->meta);
        $this->assertEquals('127.0.0.1', $submission->meta['ip_address']);
    }

    public function test_meta_field_is_cast_to_array()
    {
        $submission = ContactSubmission::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message',
            'meta' => ['ip_address' => '192.168.1.1'],
        ]);

        $this->assertIsArray($submission->meta);
        $this->assertEquals('192.168.1.1', $submission->meta['ip_address']);
    }

    public function test_fillable_fields_are_set()
    {
        $submission = new ContactSubmission();
        
        $expectedFillable = ['name', 'email', 'subject', 'message', 'meta'];
        $this->assertEquals($expectedFillable, $submission->getFillable());
    }
}
