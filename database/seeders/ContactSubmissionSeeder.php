<?php

namespace Database\Seeders;

use App\Models\ContactSubmission;
use Illuminate\Database\Seeder;

class ContactSubmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ContactSubmission::factory(25)->create();
    }
}
