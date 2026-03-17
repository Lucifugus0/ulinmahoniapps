import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/repositories/uploadid_repository.dart';

final uploadIdRepositoryProvider =
    Provider<UploadIdRepository>((ref) {
  return UploadIdRepository();
});
