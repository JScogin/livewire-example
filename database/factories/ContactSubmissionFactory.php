<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContactSubmission>
 */
class ContactSubmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'subject' => $this->getRandomSubject(),
            'message' => $this->getRandomMessage(),
            'meta' => [
                'ip_address' => fake()->ipv4(),
                'user_agent' => fake()->userAgent(),
            ],
        ];
    }

    private function getRandomSubject(): string
    {
        $subjects = [
            'General Inquiry',
            'Support Request',
            'Partnership Opportunity',
            'Product Question',
            'Technical Issue',
            'Billing Question',
            'Feature Request',
            'Feedback',
            'Complaint',
            'Praise',
            'Job Application',
            'Media Inquiry',
            'Sales Question',
            'Integration Help',
            'API Documentation',
        ];

        return fake()->randomElement($subjects);
    }

    private function getRandomMessage(): string
    {
        $messageTypes = [
            'Hello, I am interested in learning more about your services. Could you please provide me with additional information?',
            'I am experiencing an issue with your product. The problem occurs when I try to save my work. Can you help me resolve this?',
            'I would like to discuss a potential partnership opportunity. We believe there could be mutual benefits for both our companies.',
            'I have a question about your pricing structure. Do you offer any discounts for annual subscriptions?',
            'I wanted to provide some feedback about your service. Overall, I am very satisfied, but I think there could be improvements in the user interface.',
            'I am interested in applying for a position at your company. Do you have any open positions in software development?',
            'I am a journalist writing an article about your industry. Would you be available for an interview?',
            'I am having trouble integrating your API with my application. Could you provide some guidance?',
            'I would like to request a new feature. It would be helpful to have the ability to export data in CSV format.',
            'I am very happy with your service and wanted to thank you for the excellent support you provided.',
        ];

        return fake()->randomElement($messageTypes);
    }
}
