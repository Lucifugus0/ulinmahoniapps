# Register Without Verification API

## Endpoint

```
POST /api/v1/register-without-verification
```

## Description

Register a new user without requiring email verification. The user will be immediately logged in upon successful registration.

## Request Headers

| Header | Value |
|--------|-------|
| Content-Type | application/json |
| Accept | application/json |

## Request Body

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| first_name | string | Yes | User's first name (max 255 characters) |
| last_name | string | Yes | User's last name (max 255 characters) |
| username | string | Yes | Unique username (max 255, alphanumeric, dashes, underscores only) |
| email | string | Yes | Unique email address (max 255 characters) |
| password | string | Yes | Password (must meet password requirements) |
| phone_number | string | No | Unique phone number (max 20 characters) |
| nik | string | No | Unique NIK/ID number (max 16 characters) |

## Example Request

```json
{
  "first_name": "John",
  "last_name": "Doe",
  "username": "johndoe",
  "email": "john.doe@example.com",
  "password": "SecurePass123!",
  "phone_number": "081234567890",
  "nik": "1234567890123456"
}
```

## Responses

### Success (201 Created)

```json
{
  "status": "success",
  "message": "Registration successful.",
  "data": {
    "user": {
      "id": 1,
      "username": "johndoe",
      "email": "john.doe@example.com",
      "first_name": "John",
      "last_name": "Doe",
      "status": 1,
      "is_admin": 0,
      "profile_photo_url": null,
      "email_verified_at": "2024-01-15T10:30:00.000000Z"
    },
    "token": "1|abc123xyz...",
    "requires_email_verification": false
  }
}
```

### Validation Error (422 Unprocessable Entity)

```json
{
  "status": "error",
  "message": "Validation failed",
  "errors": {
    "email": ["The email has already been taken."],
    "username": ["The username has already been taken."]
  }
}
```

### Server Error (500 Internal Server Error)

```json
{
  "status": "error",
  "message": "Registration failed",
  "error": "Error message details"
}
```

## Notes

- Email is automatically marked as verified upon registration
- A Sanctum authentication token is returned for immediate API access
- The `username` field only allows alphanumeric characters, dashes, and underscores
- The `nik` field is for Indonesian National ID (NIK - Nomor Induk Kependudukan)
