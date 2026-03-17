# Promo Banner API Documentation

## Base URL
```
{{hostname}}/api/v1/promo-banner
```

## Authentication
All endpoints require `X-API-KEY` header.

---

## Endpoints

### 1. List All Promo Banners
```
GET /api/v1/promo-banner
```

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| id | integer | Get single banner by ID |
| status | integer | Filter by status (1=active, 0=inactive) |
| limit | integer | Items per page |
| page | integer | Page number |

**Examples:**
```
GET /api/v1/promo-banner                    # All banners
GET /api/v1/promo-banner?id=1               # Single banner
GET /api/v1/promo-banner?status=1           # Active banners only
GET /api/v1/promo-banner?limit=10&page=1    # Paginated
```

**Response:**
```json
{
    "data": [
        {
            "idrec": 1,
            "title": "Summer Sale 2024",
            "descriptions": "Get 50% off on all bookings",
            "status": 1,
            "created_by": 5,
            "updated_by": null,
            "thumbnail": "http://localhost/storage/promo-banners/abc123.jpg",
            "images": [
                {
                    "id": 1,
                    "image": "http://localhost/storage/promo-banners/abc123.jpg",
                    "thumbnail": "http://localhost/storage/promo-banners/thumbnails/abc123.jpg",
                    "caption": "Main banner",
                    "sort_order": 0
                }
            ]
        }
    ]
}
```

---

### 2. Create Promo Banner
```
POST /api/v1/promo-banner
```

**Request Body (JSON):**
```json
{
    "title": "Summer Sale 2024",
    "descriptions": "Get 50% off on all bookings",
    "status": 1,
    "created_by": 5,
    "images": [
        {
            "image": "data:image/jpeg;base64,/9j/4AAQSkZJRg...",
            "thumbnail": "data:image/jpeg;base64,/9j/4AAQSkZJRg...",
            "caption": "Main banner image",
            "sort_order": 0
        }
    ]
}
```

**Fields:**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| title | string | Yes | Banner title (max 255 chars) |
| descriptions | string | No | Banner description |
| status | integer | No | 1=active, 0=inactive (default: 1) |
| created_by | integer | Yes | User ID (must exist in users table) |
| images | array | No | Array of images |
| images.*.image | string | Yes* | Base64 encoded image or file path |
| images.*.thumbnail | string | No | Base64 encoded thumbnail |
| images.*.caption | string | No | Image caption (max 255 chars) |
| images.*.sort_order | integer | No | Display order (default: array index) |

**Response (201):**
```json
{
    "message": "Promo banner created successfully",
    "created_id": 1,
    "images": [
        {
            "idrec": 1,
            "promo_banner_id": 1,
            "image": "promo-banners/abc123-uuid.jpg",
            "thumbnail": "promo-banners/thumbnails/def456-uuid.jpg",
            "caption": "Main banner image",
            "sort_order": 0
        }
    ]
}
```

---

### 3. Update Promo Banner
```
PUT /api/v1/promo-banner/{id}
```

**Request Body (JSON):**
```json
{
    "title": "Updated Title",
    "descriptions": "Updated description",
    "status": 0,
    "updated_by": 5
}
```

**Fields:**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| title | string | No | Banner title |
| descriptions | string | No | Banner description |
| status | integer | No | 1=active, 0=inactive |
| image_id | integer | No | Set primary image ID |
| updated_by | integer | Yes | User ID who updated |

**Response (200):**
```json
{
    "message": "Promo banner updated successfully",
    "data": {
        "idrec": 1,
        "title": "Updated Title",
        "descriptions": "Updated description",
        "status": 0,
        "updated_by": 5
    }
}
```

---

### 4. Delete Promo Banner
```
DELETE /api/v1/promo-banner/{id}
```

**Response (204):**
```json
{
    "response": {
        "message": "Promo banner deleted successfully",
        "status_code": 204
    }
}
```

> **Note:** Deleting a banner will cascade delete all associated images.

---

### 5. Add Images to Existing Banner
```
POST /api/v1/promo-banner/{id}/images
```

**Request Body (JSON):**
```json
{
    "images": [
        {
            "image": "data:image/jpeg;base64,/9j/4AAQSkZJRg...",
            "thumbnail": "data:image/jpeg;base64,/9j/4AAQSkZJRg...",
            "caption": "Additional image",
            "sort_order": 1
        }
    ]
}
```

**Response (201):**
```json
{
    "message": "Images added successfully",
    "data": [
        {
            "idrec": 2,
            "promo_banner_id": 1,
            "image": "promo-banners/xyz789-uuid.jpg",
            "thumbnail": null,
            "caption": "Additional image",
            "sort_order": 1
        }
    ]
}
```

---

### 6. Remove Image from Banner
```
DELETE /api/v1/promo-banner/{id}/images/{imageId}
```

**Response (204):**
```json
{
    "response": {
        "message": "Image removed successfully",
        "status_code": 204
    }
}
```

---

## Image Upload Options

### Option 1: Base64 Encoded (Recommended for API)
```json
{
    "image": "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD..."
}
```
- Supports: jpeg, jpg, png, gif, webp
- Images saved to: `storage/app/public/promo-banners/`
- Thumbnails saved to: `storage/app/public/promo-banners/thumbnails/`

### Option 2: File Path (If already on server)
```json
{
    "image": "existing/path/to/image.jpg"
}
```

---

## Error Responses

**400 Bad Request:**
```json
{
    "response": {
        "message": "The title field is required.",
        "errors": null,
        "status_code": 400
    }
}
```

**404 Not Found:**
```json
{
    "response": {
        "message": "Promo banner not found",
        "errors": null,
        "status_code": 404
    }
}
```

**500 Internal Server Error:**
```json
{
    "response": {
        "message": "Error message details",
        "errors": null,
        "status_code": 500
    }
}
```

---

## Notes

1. Run `php artisan storage:link` to make uploaded images publicly accessible
2. `created_by` must reference a valid user ID in the `users` table
3. First uploaded image is automatically set as the primary image (`image_id`)
