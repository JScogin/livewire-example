<?php

namespace Tests\Feature;

use App\Livewire\ContactSubmissionList;
use App\Models\ContactSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContactSubmissionListTest extends TestCase
{
    use RefreshDatabase;

    public function test_submission_list_renders()
    {
        Livewire::test(ContactSubmissionList::class)
            ->assertSee('Contact Submissions')
            ->assertSee('Search by name, email, or subject');
    }

    public function test_displays_contact_submissions()
    {
        // Create test submissions
        ContactSubmission::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
        ]);

        ContactSubmission::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'subject' => 'Another Subject',
        ]);

        Livewire::test(ContactSubmissionList::class)
            ->assertSee('John Doe')
            ->assertSee('john@example.com')
            ->assertSee('Test Subject')
            ->assertSee('Jane Smith')
            ->assertSee('jane@example.com')
            ->assertSee('Another Subject');
    }

    public function test_search_functionality_works()
    {
        ContactSubmission::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
        ]);

        ContactSubmission::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'subject' => 'Another Subject',
        ]);

        Livewire::test(ContactSubmissionList::class)
            ->set('search', 'John')
            ->assertSee('John Doe')
            ->assertDontSee('Jane Smith');
    }

    public function test_search_by_email_works()
    {
        ContactSubmission::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        ContactSubmission::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
        ]);

        Livewire::test(ContactSubmissionList::class)
            ->set('search', 'john@example.com')
            ->assertSee('John Doe')
            ->assertDontSee('Jane Smith');
    }

    public function test_search_by_subject_works()
    {
        ContactSubmission::factory()->create([
            'name' => 'John Doe',
            'subject' => 'Support Request',
        ]);

        ContactSubmission::factory()->create([
            'name' => 'Jane Smith',
            'subject' => 'Feature Request',
        ]);

        Livewire::test(ContactSubmissionList::class)
            ->set('search', 'Support')
            ->assertSee('John Doe')
            ->assertDontSee('Jane Smith');
    }

    public function test_pagination_works()
    {
        // Create 15 submissions
        ContactSubmission::factory(15)->create();

        Livewire::test(ContactSubmissionList::class)
            ->assertSee('Showing 10 of 15 total submissions')
            ->set('perPage', 25)
            ->assertSee('Showing 15 of 15 total submissions');
    }

    public function test_empty_state_displays_when_no_submissions()
    {
        Livewire::test(ContactSubmissionList::class)
            ->assertSee('No submissions yet')
            ->assertSee('Contact form submissions will appear here');
    }

    public function test_empty_state_displays_when_no_search_results()
    {
        ContactSubmission::factory()->create([
            'name' => 'John Doe',
        ]);

        Livewire::test(ContactSubmissionList::class)
            ->set('search', 'NonExistentName')
            ->assertSee('No submissions found')
            ->assertSee('Try adjusting your search terms');
    }
}
