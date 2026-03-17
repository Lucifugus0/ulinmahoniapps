import 'dart:io';

class BookingImageUploadParams {
  final String idrec;
  final File imageFile;

  BookingImageUploadParams({
    required this.idrec,
    required this.imageFile,
  });
}
