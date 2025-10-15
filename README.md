# Livewire Contact Form Example

A minimal contact form built with Laravel 12, Livewire 3, and TailwindCSS 4. This project demonstrates modern web development practices including server-side validation, spam protection, email notifications, and an admin interface for managing submissions.

## Features

- **Contact Form**: Clean, responsive form with real-time validation
- **Spam Protection**: Honeypot field to prevent automated submissions
- **Email Notifications**: Automatic email alerts for new submissions
- **Admin Panel**: Unauthenticated list view with search and pagination
- **Responsive Design**: Mobile-first approach with TailwindCSS
- **Accessibility**: Proper labels, focus states, and keyboard navigation
- **Testing**: Comprehensive unit and feature tests

## Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and npm
- SQLite (included) or MySQL/PostgreSQL

## Installation

1. **Clone the repository**
   ```bash
   git clone git@github.com:JScogin/livewire-example.git
   cd livewire-example
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Build assets**
   ```bash
   npm run build
   ```

## Running the Application

### Development Server
```bash
php artisan serve
```
The application will be available at `http://localhost:8000`

### With Queue Worker (for email processing)
```bash
# Terminal 1: Start the application
php artisan serve

# Terminal 2: Start the queue worker
php artisan queue:work
```

### With Vite (for asset compilation)
```bash
# Terminal 1: Start the application
php artisan serve

# Terminal 2: Start Vite
npm run dev
```

## Usage

### Contact Form
- Visit `/contact` to access the contact form
- Fill out the form with name, email, subject, and message
- Form includes client-side and server-side validation
- Successful submissions are saved to the database and trigger email notifications

### Admin Panel
- Visit `/admin/submissions` to view all contact form submissions
- Search submissions by name, email, or subject
- Paginate through results (10, 25, or 50 per page)
- No authentication required (as per requirements)

## Testing

Run the test suite:
```bash
php artisan test
```

Run specific test files:
```bash
# Unit tests
php artisan test tests/Unit/ContactSubmissionTest.php

# Feature tests
php artisan test tests/Feature/ContactFormTest.php
php artisan test tests/Feature/ContactSubmissionListTest.php
```

## Email Configuration

The application sends email notifications to `gtest@mailgrove.com` by default. Configure your email settings in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@example.com
MAIL_FROM_NAME="Your App Name"

# Target email for contact form notifications
MAIL_TARGET=gtest@mailgrove.com
```

## Database Seeding

The application includes a seeder that creates 25 sample contact submissions for testing:

```bash
php artisan db:seed --class=ContactSubmissionSeeder
```

## Routes

- `/` - Homepage with project introduction
- `/contact` - Contact form
- `/admin/submissions` - Admin panel for viewing submissions

## Technical Decisions

### Framework Choices
- **Laravel 12**: Latest LTS version with modern features
- **Livewire 3**: Full-page components for reactive forms without JavaScript
- **TailwindCSS 4**: Utility-first CSS framework for rapid styling

### Architecture Decisions
- **Livewire Full-Page Components**: Simplified routing without separate controllers
- **Honeypot Spam Protection**: Lightweight alternative to CAPTCHA
- **Unauthenticated Admin**: Simplified access for demonstration purposes
- **SQLite Database**: Zero-configuration database for easy setup

### Security Features
- **CSRF Protection**: Built-in Laravel CSRF tokens
- **Mass Assignment Protection**: Explicitly defined fillable fields
- **Input Validation**: Server-side validation with Livewire
- **Spam Protection**: Honeypot field to catch automated submissions

### Performance Considerations
- **Database Indexing**: Indexes on email and created_at fields
- ****Pagination**: Efficient data loading with Laravel pagination
- ****Query Optimization**: Minimal database queries with proper relationships

## File Structure

```
app/
├── Livewire/
│   ├── ContactForm.php              # Contact form component
│   └── ContactSubmissionList.php   # Admin list component
├── Mail/
│   └── ContactSubmitted.php        # Email notification class
└── Models/
    └── ContactSubmission.php       # Contact submission model

database/
├── factories/
│   └── ContactSubmissionFactory.php # Test data factory
├── migrations/
│   └── *_create_contact_submissions_table.php
└── seeders/
    └── ContactSubmissionSeeder.php  # Sample data seeder

resources/views/
├── layouts/
│   └── app.blade.php               # Main layout
├── livewire/
│   ├── contact-form.blade.php      # Contact form template
│   └── contact-submission-list.blade.php # Admin list template
└── emails/
    └── contact-submitted.blade.php # Email template

tests/
├── Feature/
│   ├── ContactFormTest.php         # Contact form tests
│   └── ContactSubmissionListTest.php # Admin list tests
└── Unit/
    └── ContactSubmissionTest.php   # Model tests
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Ensure all tests pass
6. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For questions or issues, please open an issue on the GitHub repository.
