# NEET PG LMS Platform

A production-ready Laravel 12 LMS web application for medical students preparing for NEET PG and other medical entrance exams.

## Features

### Core Features
- **Question Bank**: 3000+ MCQs with subject/topic/chapter hierarchy
- **Practice Mode**: Learn and practice questions with instant feedback
- **Mock Tests**: Full-length exams with timer and negative marking
- **Performance Analytics**: Track accuracy, weak subjects, and improvement trends
- **Subscription Plans**: Monthly, quarterly, and yearly memberships
- **Admin Dashboard**: Comprehensive management panel

### User Roles
- **Student**: Question practice, tests, analytics, profile management
- **Admin**: Question management, user management, payment reports, analytics
- **Moderator**: Content moderation, report handling (future-ready)
- **Instructor**: Content creation framework (future-ready)

### Payment Integration
- **Razorpay**: Primary payment gateway for India
- **Stripe**: International payment support
- Coupon system with usage tracking
- Invoice history and receipt management

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.3+)
- **Database**: MySQL 8.0+
- **Cache**: Redis
- **Frontend**: Blade Templates + TailwindCSS + Alpine.js
- **Authentication**: Laravel Breeze/Jetstream
- **File Upload**: Laravel Storage
- **Queue**: Redis-based queue system

## Installation

### Prerequisites
- PHP 8.3+
- MySQL 8.0+
- Redis
- Composer

### Setup Steps

1. **Clone the repository**
```bash
cd c:\xampp\htdocs\neet
```

2. **Install dependencies**
```bash
composer install
```

3. **Environment configuration**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Database setup**
```bash
php artisan migrate
php artisan db:seed
```

5. **Compile assets**
```bash
npm install
npm run dev
```

6. **Run development server**
```bash
php artisan serve
```

7. **Start Redis (in another terminal)**
```bash
redis-cli
```

8. **Process queues (in another terminal)**
```bash
php artisan queue:work
```

Access the application at `http://localhost:8000`

## Project Structure

```
neet/
├── app/                      # Application code
│   ├── Enums/               # User roles, plan types, etc.
│   ├── Events/              # Application events
│   ├── Http/                # Controllers, Middleware, Requests, Resources
│   ├── Jobs/                # Queue jobs
│   ├── Mail/                # Email templates
│   ├── Models/              # Eloquent models
│   ├── Observers/           # Model observers
│   ├── Policies/            # Authorization policies
│   ├── Services/            # Business logic services
│   └── Listeners/           # Event listeners
├── database/                 # Migrations, seeders, factories
├── resources/               # Views, CSS, JS
│   ├── css/                # Stylesheets
│   ├── js/                 # JavaScript files
│   └── views/              # Blade templates
├── routes/                  # Route definitions
│   ├── api.php
│   ├── web.php
│   ├── admin.php
│   └── student.php
├── storage/                 # Application storage
├── tests/                   # Test files
└── config/                  # Configuration files
```

## Database Schema

### Core Tables
- `users`: User accounts and authentication
- `subscriptions`: Membership plans and status
- `subjects`: Medical subjects
- `topics`: Topics within subjects
- `chapters`: Chapters within topics
- `questions`: MCQ questions with explanations
- `options`: Question options
- `tests`: Mock exams
- `test_attempts`: User test records
- `answers`: User answers
- `bookmarks`: User bookmarked questions
- `payments`: Payment transactions
- `notifications`: User notifications
- `analytics`: User performance data

See [ARCHITECTURE.md](ARCHITECTURE.md) for complete schema details.

## API Documentation

### Authentication
```
POST /api/auth/register
POST /api/auth/login
POST /api/auth/logout
GET  /api/auth/me
```

### Questions
```
GET    /api/questions                    # List all questions
GET    /api/questions/{id}               # Get single question
GET    /api/questions/chapter/{chapterId}
POST   /api/questions/{id}/bookmark      # Bookmark question
DELETE /api/questions/{id}/bookmark      # Remove bookmark
```

### Tests
```
GET    /api/tests                        # List available tests
GET    /api/tests/{id}                   # Get test details
POST   /api/tests/{id}/start             # Start test attempt
POST   /api/tests/{id}/submit-answer     # Submit answer
GET    /api/tests/{id}/result            # Get results
```

### User
```
GET  /api/user/analytics                 # Performance analytics
GET  /api/user/leaderboard               # Leaderboard ranking
GET  /api/user/bookmarks                 # User bookmarks
PUT  /api/user/profile                   # Update profile
```

### Subscriptions
```
GET  /api/subscriptions/plans            # Available plans
POST /api/subscriptions/create           # Create subscription
POST /api/subscriptions/cancel           # Cancel subscription
```

## Configuration

### Payment Gateways

**Razorpay Configuration** (.env)
```
RAZORPAY_KEY_ID=your_key_id
RAZORPAY_KEY_SECRET=your_key_secret
RAZORPAY_WEBHOOK_SECRET=your_webhook_secret
```

**Stripe Configuration** (.env)
```
STRIPE_PUBLIC_KEY=your_public_key
STRIPE_SECRET_KEY=your_secret_key
STRIPE_WEBHOOK_SECRET=your_webhook_secret
```

### Email Configuration
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
```

## Development

### Running Tests
```bash
./vendor/bin/pest
```

### Database Migrations
```bash
php artisan migrate               # Run migrations
php artisan migrate:rollback      # Rollback migrations
php artisan migrate:fresh --seed  # Fresh database with seeds
```

### Creating Models
```bash
php artisan make:model Models/Question -mfs
```

### Creating Controllers
```bash
php artisan make:controller Admin/QuestionController --model=Question
```

### Queue Jobs
```bash
php artisan make:job SendSubscriptionReminderEmail
```

## Performance Optimization

- Database query optimization with eager loading
- Redis caching for frequently accessed data
- Queue-based async processing
- Lazy loading of images
- Database indexing on key columns

## Security

- CSRF protection (Laravel built-in)
- SQL injection prevention via Eloquent ORM
- XSS protection via Blade escaping
- Role-based access control (Middleware & Policies)
- Rate limiting on APIs
- Secure payment validation
- Activity audit logging

## Deployment

### Production Checklist
- [ ] Set `APP_DEBUG=false` in .env
- [ ] Set `APP_ENV=production`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Setup Redis cache
- [ ] Configure queue workers
- [ ] Setup SSL certificate
- [ ] Configure backup strategy
- [ ] Setup monitoring/logging

### Environment Variables
```bash
APP_ENV=production
APP_DEBUG=false
DB_HOST=production_db_host
REDIS_HOST=production_redis_host
```

## Support

For issues and questions, please contact the development team or create an issue in the repository.

## License

MIT License - See LICENSE file for details

## Contributing

1. Create a feature branch
2. Make your changes
3. Submit a pull request

---

**Platform Version**: 1.0.0
**Last Updated**: 2024
