# 📱 Panduan Penggunaan Live Chat System

Dokumentasi lengkap cara menggunakan fitur Live Chat untuk berkomunikasi dengan customer tentang booking mereka.

---

## 📦 1. Setup & Generate Sample Data

### **Step 1: Generate Sample Data**

Jalankan seeder untuk membuat data sample (3 conversations dengan messages):

```bash
php artisan db:seed --class=ChatSampleDataSeeder
```

**Output yang diharapkan:**
```
🚀 Starting Chat Sample Data Seeder...
✓ Found 3 transactions

📝 Creating conversation #1 for Order ID: ORD123
  ✓ Conversation created (ID: 1)
  ✓ Added admin as staff participant
  ✓ Created 5 sample messages

📝 Creating conversation #2 for Order ID: ORD456
  ✓ Conversation created (ID: 2)
  ✓ Added admin as staff participant
  ✓ Created 3 sample messages

📝 Creating conversation #3 for Order ID: ORD789
  ✓ Conversation created (ID: 3)
  ✓ Added admin as staff participant
  ✓ Created 3 sample messages

✅ Chat sample data created successfully!
🔗 Access chat at: /chat
```

---

## 🎯 2. Cara Menggunakan Web Interface

### **Step 1: Login ke System**

1. Buka browser dan akses aplikasi
2. Login dengan akun Super Admin (User ID: 1)
3. Anda akan diarahkan ke dashboard

### **Step 2: Akses Menu Chat**

1. Lihat sidebar menu di sebelah kiri
2. Cari menu **"Chat"** dengan icon chat bubble
3. Klik menu **"Chat"** untuk membuka inbox

### **Step 3: Melihat Daftar Conversations (Inbox)**

Di halaman inbox, Anda akan melihat:

**Tabel Conversations:**
- **Order ID** - ID booking terkait
- **Title** - Judul conversation
- **Property** - Nama property/hotel
- **Messages** - Jumlah pesan (badge biru)
- **Last Message** - Waktu pesan terakhir (relative time)
- **Status** - Status conversation (Active/Archived/Closed)
- **Actions** - Tombol "Open Chat"

**Fitur Filter & Search:**
- **Search box** - Ketik Order ID atau Title untuk mencari
- **Status dropdown** - Filter berdasarkan status (All/Active/Archived/Closed)
- **Show dropdown** - Pilih jumlah data per halaman (10/20/50)

**Tips:**
- Filter otomatis berjalan saat mengetik di search box (delay 500ms)
- Klik dropdown status/show untuk filter langsung
- Pagination muncul di bagian bawah tabel

### **Step 4: Membuka Conversation (Chat Window)**

1. Klik tombol **"Open Chat"** pada conversation yang ingin dibuka
2. Anda akan diarahkan ke halaman chat window

**Di Chat Window Anda akan melihat:**

**Header Section:**
- Title conversation
- Order ID dengan icon
- Property name dengan icon
- Jumlah participants
- Status badge (Active/Archived/Closed)
- Tombol "Back to Conversations"

**Messages Section:**
- Riwayat pesan lengkap (scrollable)
- Avatar user dengan initial name
- Nama pengirim & timestamp
- Bubble message dengan warna berbeda:
  - **Biru** (kanan) - Pesan Anda sendiri
  - **Putih** (kiri) - Pesan dari user lain
- Attachment files (jika ada)
- Auto-scroll ke pesan terbaru

**Message Input Section:**
- Text area untuk ketik pesan (auto-resize)
- Tombol "Send" dengan icon
- Support multiline text

### **Step 5: Mengirim Pesan**

1. Scroll ke bagian bawah chat window
2. Klik di text area "Type your message..."
3. Ketik pesan Anda (bisa multiple lines dengan Enter)
4. Klik tombol **"Send"** atau tekan Ctrl+Enter
5. Halaman akan reload dan pesan baru muncul di bawah

**Tips:**
- Text area otomatis resize saat mengetik panjang
- Pesan akan muncul di posisi paling bawah
- Conversation "Last Message" akan terupdate di inbox

---

## 📱 3. API untuk Android Developer

### **Base Configuration**

```kotlin
// Base URL
const val BASE_URL = "http://your-domain.com/api/"

// Retrofit Configuration
val retrofit = Retrofit.Builder()
    .baseUrl(BASE_URL)
    .addConverterFactory(GsonConverterFactory.create())
    .client(okHttpClient) // with auth interceptor
    .build()
```

### **Authentication**

**Login untuk mendapatkan token:**

```kotlin
interface AuthApi {
    @POST("login")
    suspend fun login(
        @Body credentials: LoginRequest
    ): Response<LoginResponse>
}

data class LoginRequest(
    val email: String,
    val password: String
)

data class LoginResponse(
    val token: String,
    val user: User
)
```

**Gunakan token di semua request:**

```kotlin
class AuthInterceptor : Interceptor {
    override fun intercept(chain: Interceptor.Chain): Response {
        val token = SharedPrefs.getToken() // Get saved token

        val request = chain.request().newBuilder()
            .addHeader("Authorization", "Bearer $token")
            .addHeader("Accept", "application/json")
            .build()

        return chain.proceed(request)
    }
}
```

### **Chat API Endpoints**

#### **1. Get Conversations List**

```kotlin
@GET("chat/conversations")
suspend fun getConversations(
    @Query("search") search: String? = null,
    @Query("status") status: String? = null,
    @Query("page") page: Int = 1,
    @Query("per_page") perPage: Int = 20
): Response<ConversationsResponse>

data class ConversationsResponse(
    val success: Boolean,
    val data: List<Conversation>,
    val meta: Meta
)

data class Conversation(
    val id: Long,
    val order_id: String,
    val title: String?,
    val property_id: Int,
    val status: String,
    val messages_count: Int,
    val last_message_at: String?,
    val created_at: String
)

data class Meta(
    val current_page: Int,
    val last_page: Int,
    val per_page: Int,
    val total: Int
)
```

**Example Usage:**
```kotlin
// In ViewModel
viewModelScope.launch {
    val response = chatApi.getConversations(
        search = "ORD123",
        status = "active",
        page = 1
    )

    if (response.isSuccessful) {
        _conversations.value = response.body()?.data
    }
}
```

#### **2. Get Conversation Messages**

```kotlin
@GET("chat/conversations/{id}/messages")
suspend fun getMessages(
    @Path("id") conversationId: Long,
    @Query("page") page: Int = 1,
    @Query("per_page") perPage: Int = 50
): Response<MessagesResponse>

data class MessagesResponse(
    val success: Boolean,
    val data: List<Message>,
    val meta: Meta
)

data class Message(
    val id: Long,
    val conversation_id: Long,
    val sender_id: Long,
    val message_text: String,
    val message_type: String,
    val created_at: String,
    val sender: User,
    val attachments: List<Attachment>
)
```

**Example Usage:**
```kotlin
viewModelScope.launch {
    val response = chatApi.getMessages(conversationId = 1)

    if (response.isSuccessful) {
        _messages.value = response.body()?.data?.reversed() // Oldest first
    }
}
```

#### **3. Send Message**

```kotlin
@POST("chat/conversations/{id}/messages")
suspend fun sendMessage(
    @Path("id") conversationId: Long,
    @Body request: SendMessageRequest
): Response<SendMessageResponse>

data class SendMessageRequest(
    val message_text: String,
    val message_type: String = "text"
)

data class SendMessageResponse(
    val success: Boolean,
    val message: Message
)
```

**Example Usage:**
```kotlin
viewModelScope.launch {
    val request = SendMessageRequest(
        message_text = "Hello, customer service!"
    )

    val response = chatApi.sendMessage(
        conversationId = 1,
        request = request
    )

    if (response.isSuccessful) {
        val newMessage = response.body()?.message
        // Add to messages list
    }
}
```

#### **4. Upload Attachment**

```kotlin
@Multipart
@POST("chat/messages/{id}/attachments")
suspend fun uploadAttachment(
    @Path("id") messageId: Long,
    @Part file: MultipartBody.Part,
    @Part("attachment_type") attachmentType: RequestBody
): Response<UploadResponse>

data class UploadResponse(
    val success: Boolean,
    val attachment: Attachment
)
```

**Example Usage:**
```kotlin
fun uploadImage(messageId: Long, imageUri: Uri) {
    viewModelScope.launch {
        val file = File(getRealPathFromURI(imageUri))
        val requestFile = file.asRequestBody("image/*".toMediaTypeOrNull())
        val body = MultipartBody.Part.createFormData("file", file.name, requestFile)
        val type = "room_photo".toRequestBody("text/plain".toMediaTypeOrNull())

        val response = chatApi.uploadAttachment(messageId, body, type)

        if (response.isSuccessful) {
            val attachment = response.body()?.attachment
            // Display attachment
        }
    }
}
```

#### **5. Mark Messages as Read**

```kotlin
@POST("chat/conversations/{id}/read-all")
suspend fun markAllAsRead(
    @Path("id") conversationId: Long
): Response<BasicResponse>

data class BasicResponse(
    val success: Boolean,
    val message: String
)
```

**Example Usage:**
```kotlin
// Call when user opens conversation
viewModelScope.launch {
    chatApi.markAllAsRead(conversationId = 1)
}
```

#### **6. Search Messages**

```kotlin
@GET("chat/search")
suspend fun searchMessages(
    @Query("q") query: String,
    @Query("conversation_id") conversationId: Long? = null,
    @Query("page") page: Int = 1
): Response<MessagesResponse>
```

**Example Usage:**
```kotlin
viewModelScope.launch {
    val response = chatApi.searchMessages(
        query = "check-in",
        conversationId = 1
    )

    if (response.isSuccessful) {
        _searchResults.value = response.body()?.data
    }
}
```

---

## 🔄 4. Testing API dengan Postman

### **Setup Postman Collection**

1. **Get Token (Login)**
```
POST http://localhost/api/login
Content-Type: application/json

Body:
{
    "email": "admin@example.com",
    "password": "password"
}

Response:
{
    "token": "1|xxxxxxxxxxxxx",
    "user": {...}
}
```

2. **Set Environment Variable**
- Save token dari response
- Create variable `{{token}}` di Postman Environment

3. **Test Chat Endpoints**

**Get Conversations:**
```
GET http://localhost/api/chat/conversations
Authorization: Bearer {{token}}
```

**Get Messages:**
```
GET http://localhost/api/chat/conversations/1/messages
Authorization: Bearer {{token}}
```

**Send Message:**
```
POST http://localhost/api/chat/conversations/1/messages
Authorization: Bearer {{token}}
Content-Type: application/json

Body:
{
    "message_text": "Test message from Postman"
}
```

---

## 📊 5. Database Structure

### **Tables Overview**

```
t_chat_conversations (Conversations)
├── id
├── order_id (FK to t_transactions)
├── property_id (FK to m_properties)
├── title
├── status (active/archived/closed)
└── last_message_at

t_chat_messages (Messages)
├── id
├── conversation_id (FK)
├── sender_id (FK to users)
├── message_text
├── message_type (text/file/image/system)
└── created_at

t_chat_participants (Participants)
├── id
├── conversation_id (FK)
├── user_id (FK)
├── role (customer/staff/admin)
└── last_read_at

t_chat_attachments (Attachments)
├── id
├── message_id (FK)
├── file_name
├── file_path
├── file_type
└── attachment_type

t_chat_message_reads (Read Receipts)
├── id
├── message_id (FK)
├── user_id (FK)
└── read_at
```

---

## ❓ 6. FAQ & Troubleshooting

### **Q: Menu Chat tidak muncul di sidebar?**
**A:** Pastikan user Anda memiliki permission "Chat". Jalankan:
```bash
php artisan tinker
```
```php
$userId = 1; // Your user ID
$chatPermId = DB::table('permissions')->where('name', 'Chat')->value('id');
DB::table('role_permission')->insert([
    'user_id' => $userId,
    'permission_id' => $chatPermId,
    'created_by' => $userId,
    'updated_by' => $userId,
    'created_at' => now(),
    'updated_at' => now(),
]);
```

### **Q: Conversations list kosong?**
**A:** Jalankan seeder untuk generate sample data:
```bash
php artisan db:seed --class=ChatSampleDataSeeder
```

### **Q: Error saat send message?**
**A:**
1. Pastikan CSRF token valid
2. Check console browser untuk error details
3. Verify user memiliki akses ke conversation (property scoping)

### **Q: API return 401 Unauthorized?**
**A:**
1. Pastikan token Sanctum valid
2. Check format header: `Authorization: Bearer {token}`
3. Verify token belum expired

### **Q: Messages tidak muncul?**
**A:**
1. Check relationship di model
2. Verify data ada di database: `select * from t_chat_messages where conversation_id = 1`
3. Clear cache: `php artisan cache:clear`

---

## 🚀 7. Next Steps (Optional Enhancements)

Fitur yang bisa ditambahkan untuk pengalaman lebih baik:

- [ ] **Real-time Updates** - Implement WebSocket untuk message langsung muncul tanpa reload
- [ ] **Typing Indicators** - Show "User is typing..."
- [ ] **Read Receipts** - Tampilkan ✓✓ untuk message yang sudah dibaca
- [ ] **Push Notifications** - Notifikasi untuk message baru
- [ ] **File Upload UI** - Interface upload KTP, payment proof, room photos
- [ ] **Message Search** - UI untuk search messages
- [ ] **Unread Badge** - Badge count unread messages di sidebar
- [ ] **Online Status** - Show online/offline status

---

## 📞 Support

Jika ada pertanyaan atau issue:
1. Check error logs: `storage/logs/laravel.log`
2. Review API response di Network tab browser
3. Test API endpoints di Postman
4. Verify database data dengan query langsung

---

**Happy Chatting! 🎉**
