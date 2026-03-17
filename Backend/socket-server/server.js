const express = require("express");
const http = require("http");
const socketIo = require("socket.io");
const path = require("path");
const multer = require("multer");
const fs = require("fs");

const app = express();
const server = http.createServer(app);
const io = socketIo(server, {
    cors: {
        origin: "*",
        methods: ["GET", "POST"],
    },
});

// Configure multer for file uploads
const storage = multer.diskStorage({
    destination: (req, file, cb) => {
        const uploadDir = "public/uploads/ktp";
        if (!fs.existsSync(uploadDir)) {
            fs.mkdirSync(uploadDir, { recursive: true });
        }
        cb(null, uploadDir);
    },
    filename: (req, file, cb) => {
        const bookingId = req.params.bookingId;
        const ext = path.extname(file.originalname);
        cb(null, `ktp_${bookingId}${ext}`);
    },
});

const upload = multer({ storage });

// Socket.io connection
io.on("connection", (socket) => {
    console.log("New client connected:", socket.id);

    const bookingId = socket.handshake.query.bookingId;
    if (bookingId) {
        socket.join(bookingId);
    }

    socket.on("disconnect", () => {
        console.log("Client disconnected:", socket.id);
    });
});

// Upload endpoint
app.post("/upload/:bookingId", upload.single("ktp"), (req, res) => {
    if (!req.file) {
        return res.status(400).json({ error: "No file uploaded" });
    }

    const bookingId = req.params.bookingId;
    const imageUrl = `/uploads/ktp/${req.file.filename}`;

    // Notify all clients in the booking room
    io.to(bookingId).emit("imageUploaded", {
        bookingId,
        imageUrl,
    });

    res.json({
        success: true,
        imageUrl,
    });
});

// Start server
const PORT = 3000;
server.listen(PORT, "0.0.0.0", () => {
    console.log(`Socket.io server running on http://10.128.20.92:${PORT}`);
});
