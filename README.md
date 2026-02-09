Student Project for the Health Matters CMS system

# Notification Service API (PHP + MariaDB)

A lightweight Notification Service API built in PHP that allows other modules to trigger notifications through a single HTTP endpoint.  
Designed for modular systems and team projects where multiple services need a shared notification layer.

Supports:

- REST API endpoint (`/send`)
- Health check endpoint (`/health`)
- MariaDB/MySQL persistence
- Template-based messages
- Email (Mailgun-ready)
- SMS & Push (simulated)
- API key authentication
- Rate limiting
- UUID notification IDs
- Audit trail in database

Built as a single-file deployable service for easy hosting on shared university servers.

---

# Live Endpoints (Example Deployment)

Health:
GET /index.php/health

Send:
POST /index.php/send

Example deployed path:

```
https://vesta.uclan.ac.uk/~YOURUSER/jaffa/index.php/health

What This Service Does

Other modules can send a notification by making one HTTP request.

The service will:
	1.	Validate the request
	2.	Resolve recipient preferences
	3.	Render a template
	4.	Store notification in DB
	5.	Attempt delivery (email if configured)
	6.	Return structured JSON response

This keeps notification logic centralised instead of duplicated across modules.

```
# Send Notification - Example Request

curl -X POST "https://your-host/index.php/send" \
  -H "Content-Type: application/json" \
  -H "X-API-Key: YOUR_API_KEY" \
  -d '{
    "recipient_id":"11111111-1111-1111-1111-111111111111",
    "template_name":"welcome",
    "variable_data":{
      "name":"Fadi",
      "product":"Health Matters"
    }
  }'
```
# Send Notification - Example Response

```

  {
  "status": "success",
  "data": {
    "notification_id": "b7766fc4-cc6d-4bf4-a32a-71c93fd6437c",
    "event_type": "welcome",
    "channel": "Email",
    "user_id": "11111111-1111-1111-1111-111111111111",
    "status": "queued",
    "sent_at": null,
    "created_at": "2026-02-09T22:55:29+00:00"
  }
}

# Health Check

Verify the service is alive:

```
curl https://vesta.uclan.ac.uk/~fatieh/jaffa/index.php/health
```

# Response:

{
  "status": "healthy",
  "timestamp": "2026-02-09T23:05:41+00:00",
  "service": "notification-service",
  "mailgun_configured": false
}

# Authentication

Requests must include:

X-API-Key: 7f3c9a4e8b1d2f6a0c5e3b9d7a2c1e8f4b6d0a3c9e1b”

Configured in code or via environment variable:

API_KEY=7f3c9a4e8b1d2f6a0c5e3b9d7a2c1e8f4b6d0a3c9e1b”

Prevents other users on shared hosting from abusing the endpoint.

# Configuration

'db_dsn'
'db_user'
'db_pass'
'api_key'
'mailgun_api_key'
'mailgun_domain'

Environment variables are supported but not required.

# Database Schema (MariaDB / MySQL)

Notifications: 

CREATE TABLE notifications (
  notification_id CHAR(36) PRIMARY KEY,
  event_type VARCHAR(100) NOT NULL,
  channel VARCHAR(20) NOT NULL,
  user_id CHAR(36) NOT NULL,
  status VARCHAR(20) NOT NULL,
  message TEXT NOT NULL,
  sent_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

recipient_preferences: 

CREATE TABLE recipient_preferences (
  recipient_id VARCHAR(255) PRIMARY KEY,
  email VARCHAR(255),
  preferred_channel VARCHAR(50) DEFAULT 'Email'
);

Template System:

'templates' => [
  'welcome' => 'Hi {{ name }}, welcome to {{ product }}!',
]

Variables are injected safely with HTML escaping.

# Email Sending

MAILGUN_API_KEY
MAILGUN_DOMAIN

If not configured:
	•	Email sending is skipped
	•	Notification remains queued
	•	Demo still works
	•	No external dependency required for marking

# SMS & Push

Currently simulated (logged only):
	•	No third-party SMS gateway required
	•	Demonstrates channel routing logic
	•	Easy to replace with real provider later

#Architecture

Client Module
   ↓
POST /send
   ↓
NotificationService
   ↓
Template Engine
   ↓
Database Audit Log
   ↓
Channel Dispatcher
   ↓
Email / SMS / Push

Single responsibility classes:
	•	Database
	•	TemplateEngine
	•	MailgunSender
	•	NotificationService
	•	RateLimiter
	•	Validator
	•	Logger

# PostgreSQL Compatibility

Code uses PDO.

To switch to PostgreSQL:

Change DSN only:

pgsql:host=HOST;dbname=DB

No API changes required.


