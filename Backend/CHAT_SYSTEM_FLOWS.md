# Chat System User Flows

Complete guide for using the booking-based chat system from both user and admin perspectives.

---

## Table of Contents
1. [User Flow](#user-flow)
2. [Admin Flow](#admin-flow)
3. [API Endpoints Reference](#api-endpoints-reference)
4. [Response Examples](#response-examples)

---

## User Flow

### Prerequisites
- User must have a booking with `order_id`
- User must have checked in (`booking.check_in_at` is set)
- User must still be within the booking period (`transaction.check_out` > NOW())

### Step 1: User Checks Their Bookings
First, the user needs to know their `order_id` and verify they have an active booking.

```http
GET /api/v1/booking/userId/{user_id}
Headers:
  X-API-KEY: your-api-key
```

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "order_id": "UMW-260113001PA",
      "check_in": "2026-01-13 14:00:00",
      "check_out": "2026-01-20 12:00:00",
      "property_name": "Property ABC",
      "transaction_status": "confirmed"
    }
  ]
}
```

### Step 2: User Creates a Chat Conversation
User initiates a conversation about their booking.

```http
POST /api/v1/chat/conversations
Headers:
  X-API-KEY: your-api-key
  Content-Type: application/json

Body:
{
  "user_id": 1,
  "order_id": "UMW-260113001PA",
  "title": "Question about my room",
  "initial_message": "Hi, the AC in my room is not working properly"
}
```

**Success Response (201 Created):**
```json
{
  "status": "success",
  "message": "Conversation created successfully",
  "data": {
    "id": 5,
    "order_id": "UMW-260113001PA",
    "property_id": 10,
    "title": "Question about my room",
    "status": "active",
    "created_at": "2026-01-13T10:30:00.000000Z",
    "participants": [
      {
        "id": 1,
        "user_id": 1,
        "role": "customer",
        "user": {
          "id": 1,
          "first_name": "John",
          "last_name": "Doe",
          "email": "john@example.com"
        }
      }
    ]
  }
}
```

**Error Response - Invalid Booking (403 Forbidden):**
```json
{
  "status": "error",
  "message": "You do not have a valid active booking for this order. You must check in and stay within the booking period."
}
```

**Error Response - Duplicate Conversation (409 Conflict):**
```json
{
  "status": "error",
  "message": "Conversation for this order already exists",
  "data": {
    "conversation_id": 5
  }
}
```

### Step 3: User Sends Messages
User can send text messages or images about their issue.

#### Send Text Message
```http
POST /api/v1/chat/conversations/5/messages
Headers:
  X-API-KEY: your-api-key
  Content-Type: application/json

Body:
{
  "user_id": 1,
  "message_text": "The AC remote is also missing"
}
```

#### Send Image Message
```http
POST /api/v1/chat/conversations/5/messages
Headers:
  X-API-KEY: your-api-key
  Content-Type: multipart/form-data

Body (form-data):
  user_id: 1
  image: [file upload - photo.jpg]
  message_text: "Here's a photo of the AC unit" (optional)
```

**Success Response (201 Created):**
```json
{
  "status": "success",
  "message": "Message sent successfully",
  "data": {
    "id": 15,
    "conversation_id": 5,
    "sender_id": 1,
    "message_text": "The AC remote is also missing",
    "message_type": "text",
    "is_edited": false,
    "created_at": "2026-01-13T10:35:00.000000Z",
    "sender": {
      "id": 1,
      "first_name": "John",
      "last_name": "Doe",
      "email": "john@example.com"
    },
    "attachments": []
  }
}
```

### Step 4: User Views Conversation List
User can see all their active conversations.

```http
GET /api/v1/chat/conversations?user_id=1
Headers:
  X-API-KEY: your-api-key
```

**Response:**
```json
{
  "status": "success",
  "message": "Conversations retrieved successfully",
  "data": [
    {
      "id": 5,
      "order_id": "UMW-260113001PA",
      "property_id": 10,
      "title": "Question about my room",
      "status": "active",
      "last_message_at": "2026-01-13T10:35:00.000000Z",
      "unread_count": 2,
      "messages": [
        {
          "id": 14,
          "message_text": "Hi, the AC in my room is not working properly",
          "message_type": "text",
          "created_at": "2026-01-13T10:30:00.000000Z",
          "sender": {
            "id": 1,
            "first_name": "John",
            "last_name": "Doe"
          }
        },
        {
          "id": 15,
          "message_text": "The AC remote is also missing",
          "message_type": "text",
          "created_at": "2026-01-13T10:35:00.000000Z",
          "sender": {
            "id": 1,
            "first_name": "John",
            "last_name": "Doe"
          }
        }
      ],
      "participants": [
        {
          "id": 1,
          "role": "customer",
          "user": {
            "id": 1,
            "first_name": "John",
            "last_name": "Doe"
          }
        }
      ],
      "property": {
        "idrec": 10,
        "name": "Property ABC"
      }
    }
  ]
}
```

### Step 5: User Views Specific Conversation
Get detailed conversation with all messages.

```http
GET /api/v1/chat/conversations/5?user_id=1
Headers:
  X-API-KEY: your-api-key
```

**Response:**
```json
{
  "status": "success",
  "message": "Conversation retrieved successfully",
  "data": {
    "conversation": {
      "id": 5,
      "order_id": "UMW-260113001PA",
      "property_id": 10,
      "title": "Question about my room",
      "status": "active",
      "participants": [
        {
          "id": 1,
          "role": "customer",
          "user": {
            "id": 1,
            "first_name": "John",
            "last_name": "Doe"
          }
        },
        {
          "id": 2,
          "role": "staff",
          "user": {
            "id": 3,
            "first_name": "Staff",
            "last_name": "Member"
          }
        }
      ]
    },
    "messages": [
      {
        "id": 16,
        "message_text": "I'll send someone to fix it right away!",
        "message_type": "text",
        "sender_id": 3,
        "created_at": "2026-01-13T10:40:00.000000Z",
        "sender": {
          "id": 3,
          "first_name": "Staff",
          "last_name": "Member"
        }
      },
      {
        "id": 15,
        "message_text": "The AC remote is also missing",
        "message_type": "text",
        "sender_id": 1,
        "created_at": "2026-01-13T10:35:00.000000Z",
        "sender": {
          "id": 1,
          "first_name": "John",
          "last_name": "Doe"
        }
      }
    ],
    "meta": {
      "current_page": 1,
      "last_page": 1,
      "per_page": 50,
      "total": 2
    }
  }
}
```

### Step 6: User Edits Their Message (Optional)
User can edit their own messages.

```http
PUT /api/v1/chat/messages/15
Headers:
  X-API-KEY: your-api-key
  Content-Type: application/json

Body:
{
  "user_id": 1,
  "message_text": "The AC remote is also missing and the TV is not working"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Message updated successfully",
  "data": {
    "id": 15,
    "message_text": "The AC remote is also missing and the TV is not working",
    "is_edited": true,
    "edited_at": "2026-01-13T10:45:00.000000Z"
  }
}
```

### Step 7: User Marks Messages as Read
Mark all messages in a conversation as read.

```http
POST /api/v1/chat/conversations/5/read
Headers:
  X-API-KEY: your-api-key
  Content-Type: application/json

Body:
{
  "user_id": 1
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Messages marked as read"
}
```

---

## Admin Flow

### Prerequisites
- Admin user must have `is_admin = 1` in the users table
- Admin needs to know their `admin_user_id`

### Step 1: Admin Views All Conversations
Admin can see all conversations across all properties.

```http
GET /api/v1/chat/admin/conversations?admin_user_id=2
Headers:
  X-API-KEY: your-api-key
```

**Optional Filters:**
- `?admin_user_id=2&property_id=10` - Filter by specific property
- `?admin_user_id=2&status=active` - Filter by status (active/archived/closed)
- `?admin_user_id=2&order_id=UMW-260113001PA` - Filter by order ID
- `?admin_user_id=2&limit=20` - Paginate results

**Response:**
```json
{
  "status": "success",
  "message": "Conversations retrieved successfully",
  "data": [
    {
      "id": 5,
      "order_id": "UMW-260113001PA",
      "property_id": 10,
      "title": "Question about my room",
      "status": "active",
      "last_message_at": "2026-01-13T10:40:00.000000Z",
      "messages": [
        {
          "id": 14,
          "message_text": "Hi, the AC in my room is not working properly",
          "sender_id": 1,
          "message_type": "text",
          "created_at": "2026-01-13T10:30:00.000000Z",
          "sender": {
            "id": 1,
            "first_name": "John",
            "last_name": "Doe"
          },
          "attachments": []
        },
        {
          "id": 15,
          "message_text": "The AC remote is also missing",
          "sender_id": 1,
          "message_type": "text",
          "created_at": "2026-01-13T10:35:00.000000Z",
          "sender": {
            "id": 1,
            "first_name": "John",
            "last_name": "Doe"
          },
          "attachments": []
        }
      ],
      "participants": [
        {
          "id": 1,
          "role": "customer",
          "user": {
            "id": 1,
            "first_name": "John",
            "last_name": "Doe",
            "email": "john@example.com"
          }
        }
      ],
      "property": {
        "idrec": 10,
        "name": "Property ABC"
      },
      "transaction": {
        "idrec": 25,
        "order_id": "UMW-260113001PA",
        "user_name": "John Doe",
        "user_email": "john@example.com"
      }
    }
  ]
}
```

### Step 2: Admin Assigns Staff to Handle Conversation
Admin assigns a staff member or another admin to help with the conversation.

```http
POST /api/v1/chat/conversations/5/participants
Headers:
  X-API-KEY: your-api-key
  Content-Type: application/json

Body:
{
  "admin_user_id": 2,
  "user_id": 3,
  "role": "staff"
}
```

**Note:**
- `admin_user_id` = The admin doing the assignment (must have `is_admin = 1`)
- `user_id` = The staff/admin being assigned to the conversation
- `role` = Either "staff" or "admin"

**Success Response (201 Created):**
```json
{
  "status": "success",
  "message": "Participant assigned successfully",
  "data": {
    "id": 2,
    "conversation_id": 5,
    "user_id": 3,
    "role": "staff",
    "joined_at": "2026-01-13T10:38:00.000000Z",
    "user": {
      "id": 3,
      "first_name": "Staff",
      "last_name": "Member",
      "email": "staff@example.com"
    }
  }
}
```

**System Message Created:**
A system message is automatically created: "Staff Member has been added as staff"

**Error - Already Participant (409 Conflict):**
```json
{
  "status": "error",
  "message": "User is already a participant in this conversation"
}
```

### Step 3: Assigned Staff/Admin Replies to User
Once assigned, the staff member can reply using the regular message endpoint.

```http
POST /api/v1/chat/conversations/5/messages
Headers:
  X-API-KEY: your-api-key
  Content-Type: application/json

Body:
{
  "user_id": 3,
  "message_text": "Hello! I'll send a technician to your room within 30 minutes to fix the AC and bring a new remote."
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Message sent successfully",
  "data": {
    "id": 16,
    "conversation_id": 5,
    "sender_id": 3,
    "message_text": "Hello! I'll send a technician to your room within 30 minutes to fix the AC and bring a new remote.",
    "message_type": "text",
    "is_edited": false,
    "created_at": "2026-01-13T10:40:00.000000Z",
    "sender": {
      "id": 3,
      "first_name": "Staff",
      "last_name": "Member"
    },
    "attachments": []
  }
}
```

### Step 4: Staff Sends Image/File to User
Staff can also send images (e.g., photos of replacement items, instructions).

```http
POST /api/v1/chat/conversations/5/messages
Headers:
  X-API-KEY: your-api-key
  Content-Type: multipart/form-data

Body (form-data):
  user_id: 3
  image: [file upload - new_remote.jpg]
  message_text: "Here's what the new remote looks like"
```

### Step 5: Admin Views Specific Conversation
Admin can view any conversation in detail.

```http
GET /api/v1/chat/conversations/5?user_id=2
Headers:
  X-API-KEY: your-api-key
```

**Note:** Admin doesn't need to be a participant to view. The system checks if the user is an admin.

### Step 6: Admin/Staff Marks Messages as Read
Track that staff has read customer messages.

```http
POST /api/v1/chat/conversations/5/read
Headers:
  X-API-KEY: your-api-key
  Content-Type: application/json

Body:
{
  "user_id": 3
}
```

---

## API Endpoints Reference

### User Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/api/v1/chat/conversations` | List user's conversations | user_id |
| GET | `/api/v1/chat/conversations/{id}` | Get conversation details | user_id |
| POST | `/api/v1/chat/conversations` | Create new conversation | user_id |
| POST | `/api/v1/chat/conversations/{id}/messages` | Send message | user_id |
| POST | `/api/v1/chat/conversations/{id}/read` | Mark as read | user_id |
| PUT | `/api/v1/chat/messages/{id}` | Edit message | user_id |

### Admin Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/api/v1/chat/admin/conversations` | List all conversations | admin_user_id |
| POST | `/api/v1/chat/conversations/{id}/participants` | Assign staff/admin | admin_user_id |

---

## Response Examples

### Success Response Format
```json
{
  "status": "success",
  "message": "Operation successful",
  "data": { ... }
}
```

### Error Response Format
```json
{
  "status": "error",
  "message": "Error description",
  "errors": { ... }
}
```

### Common Error Codes

| Status Code | Meaning | Example |
|------------|---------|---------|
| 400 | Bad Request | Missing user_id parameter |
| 403 | Forbidden | Invalid booking or unauthorized |
| 404 | Not Found | Conversation or message not found |
| 409 | Conflict | Duplicate conversation |
| 422 | Validation Error | Invalid input data |
| 500 | Server Error | Internal server error |

---

## Message Types

| Type | Description | Example |
|------|-------------|---------|
| text | Plain text message | "Hello, I need help" |
| image | Image with optional text | Photo of broken AC + "It's not working" |
| system | Auto-generated system message | "Staff Member has been added as staff" |

---

## Conversation Status

| Status | Description |
|--------|-------------|
| active | Conversation is ongoing |
| archived | Conversation is archived but can be reopened |
| closed | Conversation is permanently closed |

---

## Participant Roles

| Role | Description | Can Do |
|------|-------------|--------|
| customer | User who created conversation | Send messages, edit own messages |
| staff | Assigned staff member | Send messages, view conversation |
| admin | Administrator | Send messages, view all conversations, assign participants |

---

## Image Upload Specifications

- **Allowed Formats:** JPEG, JPG, PNG, GIF
- **Maximum Size:** 10MB
- **Storage Location:** `storage/app/public/chat_attachments/{conversation_id}/`
- **Thumbnail:** Optional (requires Intervention Image package)
- **File Naming:** Random 40-character string + extension

---

## Complete User Journey Example

```
1. User checks in to Property ABC
   └─> booking.check_in_at is set

2. User discovers AC issue
   └─> Creates conversation: POST /chat/conversations
       {user_id: 1, order_id: "UMW-260113001PA", ...}

3. User sends initial message
   └─> POST /chat/conversations/5/messages
       {user_id: 1, message_text: "AC not working"}

4. User uploads photo of issue
   └─> POST /chat/conversations/5/messages
       {user_id: 1, image: photo.jpg}

5. Admin sees new conversation
   └─> GET /chat/admin/conversations?admin_user_id=2

6. Admin assigns staff member
   └─> POST /chat/conversations/5/participants
       {admin_user_id: 2, user_id: 3, role: "staff"}

7. Staff receives notification and replies
   └─> POST /chat/conversations/5/messages
       {user_id: 3, message_text: "I'll send help!"}

8. User sees reply
   └─> GET /chat/conversations?user_id=1
       (sees unread_count: 1)

9. User views conversation
   └─> GET /chat/conversations/5?user_id=1
       (automatically marks as read)

10. Issue resolved, conversation remains in history
    └─> User checks out, can no longer create new messages
```

---

## Testing Checklist

### User Testing
- [ ] Create conversation with valid booking
- [ ] Try to create conversation without check-in (should fail)
- [ ] Try to create conversation after check-out (should fail)
- [ ] Send text message
- [ ] Send image message
- [ ] Edit own message
- [ ] View conversation list
- [ ] View specific conversation
- [ ] Mark messages as read

### Admin Testing
- [ ] View all conversations
- [ ] Filter by property
- [ ] Filter by status
- [ ] Assign staff to conversation
- [ ] Try to assign duplicate participant (should fail)
- [ ] Reply to customer as assigned staff
- [ ] View conversation without being participant (admin bypass)

### Error Testing
- [ ] Missing user_id parameter
- [ ] Invalid user_id
- [ ] Non-existent conversation
- [ ] Duplicate conversation creation
- [ ] Edit message by non-sender
- [ ] Upload oversized image
- [ ] Upload non-image file
- [ ] Access conversation without permission

---

## Notes

1. **Booking Validation:** Users can only chat during active stay (after check-in, before check-out)
2. **Manual Assignment:** Admins must manually assign staff to conversations
3. **No Auto-Assignment:** System does NOT auto-assign based on property ownership
4. **Conversation Persistence:** Conversations remain in history after checkout
5. **Message Ordering:** Messages are ordered by created_at (oldest first in conversation view)
6. **Unread Counts:** Calculated using participant's last_read_at timestamp
7. **System Messages:** Cannot be edited or deleted
8. **Admin Access:** Admins can view and participate in any conversation

---

## Support

For issues or questions, please contact the development team or refer to the main implementation documentation.
