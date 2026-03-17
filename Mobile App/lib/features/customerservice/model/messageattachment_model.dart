/// Model for message attachments (images, PDFs, documents)
class MessageAttachment {
  final int id;
  final String fileName;
  final String fileUrl;
  final String fileType; // 'image', 'pdf', 'document'
  final int fileSize; // in bytes
  final DateTime uploadedAt;

  MessageAttachment({
    required this.id,
    required this.fileName,
    required this.fileUrl,
    required this.fileType,
    required this.fileSize,
    required this.uploadedAt,
  });

  /// Factory constructor from JSON
  factory MessageAttachment.fromJson(Map<String, dynamic> json) {
    return MessageAttachment(
      id: json['id'] as int,
      fileName: json['file_name'] as String,
      fileUrl: json['file_url'] as String,
      fileType: json['file_type'] as String? ?? 'document',
      fileSize: json['file_size'] as int? ?? 0,
      uploadedAt: DateTime.parse(json['uploaded_at'] as String),
    );
  }

  /// Convert to JSON
  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'file_name': fileName,
      'file_url': fileUrl,
      'file_type': fileType,
      'file_size': fileSize,
      'uploaded_at': uploadedAt.toIso8601String(),
    };
  }

  /// Copy with method for immutability
  MessageAttachment copyWith({
    int? id,
    String? fileName,
    String? fileUrl,
    String? fileType,
    int? fileSize,
    DateTime? uploadedAt,
  }) {
    return MessageAttachment(
      id: id ?? this.id,
      fileName: fileName ?? this.fileName,
      fileUrl: fileUrl ?? this.fileUrl,
      fileType: fileType ?? this.fileType,
      fileSize: fileSize ?? this.fileSize,
      uploadedAt: uploadedAt ?? this.uploadedAt,
    );
  }

  /// Check if attachment is an image
  bool get isImage => fileType.toLowerCase() == 'image';

  /// Check if attachment is a PDF
  bool get isPdf => fileType.toLowerCase() == 'pdf';

  /// Check if attachment is a document (non-image, non-PDF)
  bool get isDocument => !isImage && !isPdf;

  /// Get file size in human-readable format
  String get formattedFileSize {
    if (fileSize < 1024) {
      return '$fileSize B';
    } else if (fileSize < 1024 * 1024) {
      return '${(fileSize / 1024).toStringAsFixed(1)} KB';
    } else {
      return '${(fileSize / (1024 * 1024)).toStringAsFixed(1)} MB';
    }
  }

  /// Get file extension from fileName
  String get fileExtension {
    final parts = fileName.split('.');
    return parts.length > 1 ? parts.last.toLowerCase() : '';
  }
}
