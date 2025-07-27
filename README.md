# Aziona AI Calling Agent

Aziona AI Calling Agent is a robust Laravel-based solution for automated voice calling and appointment management, powered by Vapi.ai. It enables businesses to place instant outbound calls, schedule future calls, manage appointments, and send email notifications—all from a secure, responsive dashboard.

---

## Product Overview
Aziona AI Calling Agent streamlines communication and scheduling for teams, support desks, and service providers. It integrates with Vapi.ai for voice automation and supports timezone-aware scheduling, secure webhooks, and real-time dashboard updates.

---

## Features
- Instant outbound voice calls via Vapi.ai
- Scheduled calls with timezone conversion
- Appointment management (CRUD)
- Email notifications for calls and appointments
- Secure webhook handler for Vapi.ai events
- Responsive dashboard (Blade + JS)
- Robust error handling and logging
- RESTful API endpoints for integration

---

## Architecture
- **Backend:** Laravel 10+, PHP 8.2+
- **Frontend:** Blade templates, custom JS
- **Voice API:** Vapi.ai
- **Database:** MySQL/Postgres
- **Mail:** SMTP/Mailtrap
- **Queue:** Laravel Queues (for jobs & emails)

---

## Setup
1. **Clone the repo:**
   ```bash
   git clone https://github.com/btwwahab/aziona-call-agent.git
   cd aziona-call-agent
   ```
2. **Install dependencies:**
   ```bash
   composer install
   npm install && npm run dev
   ```
3. **Configure environment:**
   - Copy `.env.example` to `.env`
   - Set your DB, SMTP, and Vapi.ai credentials:
     ```
     DB_DATABASE=your_db
     DB_USERNAME=your_user
     DB_PASSWORD=your_pass
     VAPI_API_KEY=your_vapi_key
     VAPI_AGENT_ID=your_vapi_agent_id
     VAPI_FROM_NUMBER=your_vapi_number
     VAPI_WEBHOOK_SECRET=your_webhook_secret
     MAIL_MAILER=smtp
     MAIL_HOST=smtp.mailtrap.io
     MAIL_PORT=2525
     MAIL_USERNAME=your_mailtrap_user
     MAIL_PASSWORD=your_mailtrap_pass
     MAIL_ENCRYPTION=null
     MAIL_FROM_ADDRESS=from@example.com
     MAIL_FROM_NAME="Aziona Alpha"
     ```
4. **Run migrations:**
   ```bash
   php artisan migrate
   ```
5. **Start the server:**
   ```bash
   php artisan serve
   ```

---

## Usage
- Access the dashboard at `http://127.0.0.1:8000`
- Place instant calls or schedule future calls
- Manage appointments and view logs
- Webhook endpoint: `/api/vapi-webhook` (for Vapi.ai)

---

## API Endpoints
- `POST /call` — Place an instant outbound call
- `POST /schedule` — Schedule a call (with timezone)
- `GET /dashboard/data` — Fetch dashboard stats
- `GET/POST/PUT/DELETE /api/appointments` — Manage appointments
- `POST /api/vapi-webhook` — Vapi.ai webhook handler

---

## Testing
- Run feature tests:
  ```bash
  php artisan test
  ```

---

## Troubleshooting
- **500 Internal Server Error:** Check `storage/logs/laravel.log` for details
- **Webhook not updating:** Verify `VAPI_WEBHOOK_SECRET` in `.env` matches Vapi.ai config
- **Email not sending:** Check SMTP credentials and queue worker status
- **Timezone issues:** Ensure frontend converts UTC to local time for display

---

## Security
- All sensitive keys are loaded from `.env` and `config/services.php`
- Webhook is protected by a secret header
- All API endpoints return valid JSON responses
- CSRF protection enabled for forms

---

## Contribution
Pull requests and issues are welcome! Please follow PSR standards and include tests for new features.

---

## License
MIT
