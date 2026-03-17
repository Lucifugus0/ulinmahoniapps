# Ulin Mahoni API Documentation

## Authentication Endpoints

### Register
- **URL**: `/api/register`
- **Method**: `POST`
- **Request Body**:
```json
{
    "username": "string",
    "email": "string",
    "password": "string",
    "password_confirmation": "string"
}
```
- **Success Response (201)**:
```json
{
    "status": "success",
    "message": "Registration successful",
    "data": {
        "user": {
            "id": "integer",
            "username": "string",
            "email": "string",
            "status": "integer",
            "is_admin": "boolean",
            "profile_photo_url": "string"
        },
        "token": "string"
    }
}
```

### Login
- **URL**: `/api/login`
- **Method**: `POST`
- **Request Body**:
```json
{
    "email": "string",
    "password": "string"
}
```
- **Success Response (200)**:
```json
{
    "status": "success",
    "message": "Login successful",
    "data": {
        "user": {
            "id": "integer",
            "username": "string",
            "email": "string",
            "status": "integer",
            "is_admin": "boolean",
            "profile_photo_url": "string"
        },
        "token": "string"
    }
}
```

### Logout
- **URL**: `/api/logout`
- **Method**: `POST`
- **Headers**: 
  - `Authorization: Bearer {token}`
- **Success Response (200)**:
```json
{
    "status": "success",
    "message": "Successfully logged out"
}
```

### Get Profile
- **URL**: `/api/profile`
- **Method**: `GET`
- **Headers**: 
  - `Authorization: Bearer {token}`
- **Success Response (200)**:
```json
{
    "status": "success",
    "data": {
        "user": {
            "id": "integer",
            "username": "string",
            "email": "string",
            "status": "integer",
            "is_admin": "boolean",
            "profile_photo_url": "string"
        }
    }
}
```

### Update Profile
- **URL**: `/api/profile`
- **Method**: `PUT`
- **Headers**: 
  - `Authorization: Bearer {token}`
- **Request Body**:
```json
{
    "username": "string",
    "email": "string"
}
```
- **Success Response (200)**:
```json
{
    "status": "success",
    "message": "Profile updated successfully",
    "data": {
        "user": {
            "id": "integer",
            "username": "string",
            "email": "string",
            "status": "integer",
            "is_admin": "boolean",
            "profile_photo_url": "string"
        }
    }
}
```

### Update Password
- **URL**: `/api/profile/password`
- **Method**: `PUT`
- **Headers**: 
  - `Authorization: Bearer {token}`
- **Request Body**:
```json
{
    "current_password": "string",
    "password": "string",
    "password_confirmation": "string"
}
```
- **Success Response (200)**:
```json
{
    "status": "success",
    "message": "Password updated successfully"
}
```

## Error Responses

### Validation Error (422)
```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "field": [
            "error message"
        ]
    }
}
```

### Authentication Error (401)
```json
{
    "status": "error",
    "message": "Invalid credentials"
}
```

### Server Error (500)
```json
{
    "status": "error",
    "message": "Error message",
    "error": "Detailed error message"
}
```

## Password Requirements
- Minimum 8 characters
- At least one uppercase letter
- At least one lowercase letter
- At least one number
- At least one special character

## Authentication
All protected endpoints require a Bearer token in the Authorization header:
```
Authorization: Bearer {your_token}
```

## Rate Limiting
- Login attempts are limited to 5 per minute per IP address
- API requests are limited to 60 per minute per token

## CORS
CORS is enabled for all origins in development. In production, you should configure the allowed origins in your `.env` file:
```
CORS_ALLOWED_ORIGINS=https://yourdomain.com,https://app.yourdomain.com
```
