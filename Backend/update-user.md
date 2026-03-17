# Update User API

## Endpoint

```
PUT /api/v1/users/{id}
```

## Description

Update an existing user's information. All fields are optional - only include the fields you want to update.

## Request Headers

| Header | Value | Required |
|--------|-------|----------|
| Content-Type | application/json | Yes |
| Accept | application/json | Yes |
| X-API-KEY | Your API key | Yes |

## URL Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | integer | Yes | The user ID to update |

## Request Body

All fields are optional. Only include the fields you want to update.

| Field | Type | Description |
|-------|------|-------------|
| first_name | string | User's first name (max 255 characters) |
| last_name | string | User's last name (max 255 characters) |
| username | string | Unique username (max 255, alphanumeric, dashes, underscores only) |
| name | string | Display name (max 255 characters) |
| email | string | Unique email address |
| phone_number | string | Unique phone number (max 20 characters) |
| nik | string | Unique NIK/ID number (max 16 characters) |
| password | string | New password (min 6 characters) |
| status | integer | User status (0 = inactive, 1 = active) |
| is_admin | integer | Admin flag (0 = regular user, 1 = admin) |
| profile_photo_path | string | Path to profile photo (max 2048 characters) |

## Example Request

```bash
curl -X PUT "https://your-domain.com/api/v1/users/1" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "X-API-KEY: your-api-key" \
  -d '{
    "first_name": "Jane",
    "last_name": "Smith",
    "nik": "1234567890123456",
    "phone_number": "081234567890"
  }'
```

## Responses

### Success (200 OK)

```json
{
  "status": "success",
  "message": "User updated successfully",
  "data": {
    "id": 1,
    "name": "janesmith",
    "first_name": "Jane",
    "last_name": "Smith",
    "username": "janesmith",
    "email": "jane@example.com",
    "phone_number": "081234567890",
    "status": 1,
    "email_verified_at": "2024-01-15T10:30:00.000000Z",
    "profile_photo_path": null,
    "profile_photo_url": null
  }
}
```

### User Not Found (404 Not Found)

```json
{
  "status": "error",
  "message": "User not found"
}
```

### Validation Error (422 Unprocessable Entity)

```json
{
  "status": "error",
  "message": "Validation failed",
  "errors": {
    "email": ["The email has already been taken."],
    "nik": ["The nik has already been taken."]
  }
}
```

### Invalid API Key (401 Unauthorized)

```json
{
  "status": "error",
  "message": "Invalid or missing API KEY",
  "documentation": "Please contact our staff for assistance"
}
```

### Server Error (500 Internal Server Error)

```json
{
  "status": "error",
  "message": "Error updating user",
  "error": "Error message details"
}
```

## Notes

- This endpoint requires a valid `X-API-KEY` header
- Unique fields (`username`, `email`, `phone_number`, `nik`) will exclude the current user when checking for duplicates
- Password will be automatically hashed before storing
- The response returns the refreshed user data after the update
- The `nik` field is for Indonesian National ID (NIK - Nomor Induk Kependudukan)
