# notifications-service-php
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

Built as a **single-file deployable service** for easy hosting on shared university servers.

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
# Send Notification — Example Request

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

# Send Notification — Example Response

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
#Health Check

Used by tutors, testers, and other modules to verify the service is alive.

curl https://your-host/index.php/health

Response:

{
  "status": "healthy",
  "timestamp": "2026-02-09T23:05:41+00:00",
  "service": "notification-service",
  "mailgun_configured": false
}

#Authentication

Requests must include:

X-API-Key: YOUR_SECRET_KEY

Configured in code or via environment variable:



