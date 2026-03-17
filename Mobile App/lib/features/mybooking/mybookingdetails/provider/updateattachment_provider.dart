import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/repositories/updateattachment_repository.dart';

final updateAttachmentRepositoryProvider =
    Provider<UpdateAttachmentRepository>((ref) {
  return UpdateAttachmentRepository();
});