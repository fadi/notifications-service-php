# Notification Service API (PHP + MariaDB)

Student project component for the **Health Matters CMS system**.

A lightweight Notification Service API that lets other modules trigger notifications through a single HTTP endpoint.

---

## Features

- REST endpoint `/send`
- Health endpoint `/health`
- MariaDB / MySQL storage
- Template-based messages
- Mailgun-ready email
- SMS & Push (simulated)
- API key authentication
- Rate limiting
- UUID notification IDs
- Single-file deploy

---

## Endpoints

### Health

GET:

```
/index.php/health
```

Example:

```bash
curl https://vesta.uclan.ac.uk/~YOURUSER/jaffa/index.php/health
```

Response:

```json
{
  "status": "healthy",
  "timestamp": "2026-02-09T23:05:41+00:00",
  "service": "notification-service",
  "mailgun_configured": false
}
```

---

### Send Notification

POST:

```
/index.php/send
```

Example:

```bash
curl -X POST "https://your-host/index.php/send" \
  -H "Content-Type: application/json" \
  -H "X-API-Key: YOUR_API_KEY" \
  -d '{
    "recipient_id": "11111111-1111-1111-1111-111111111111",
    "template_name": "welcome",
    "variable_data": {
      "name": "Fadi",
      "product": "Health Matters"
    }
  }'
```

Response:

```json
{
  "status": "success",
  "data": {
    "notification_id": "uuid",
    "event_type": "welcome",
    "channel": "Email",
    "user_id": "uuid",
    "status": "queued",
    "sent_at": null,
    "created_at": "ISO-8601 timestamp"
  }
}
```

---

## Authentication

All POST requests require:

```
X-API-Key: YOUR_API_KEY
```

Configured via environment variable or config array.

---

## Database Schema

```sql
CREATE TABLE notifications (
  notification_id CHAR(36) PRIMARY KEY,
  event_type VARCHAR(100),
  channel VARCHAR(20),
  user_id CHAR(36),
  status VARCHAR(20),
  message TEXT,
  sent_at DATETIME NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

```sql
CREATE TABLE recipient_preferences (
  recipient_id VARCHAR(255) PRIMARY KEY,
  email VARCHAR(255),
  preferred_channel VARCHAR(50) DEFAULT 'Email'
);
```

---

## Templates

```php
'templates' => [
  'welcome' => 'Hi {{ name }}, welcome to {{ product }}!'
]
```

---

## Email

Mailgun supported via:

```
MAILGUN_API_KEY
MAILGUN_DOMAIN
```

If not configured, messages remain queued (demo mode).

---

## PostgreSQL Compatibility

Uses PDO. Switch by changing DSN:

```
pgsql:host=HOST;dbname=DB
```

No API changes required.
