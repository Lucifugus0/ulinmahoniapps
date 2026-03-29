import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/repositories/content_repository.dart';
import '../../../core/network/api_result.dart';

/// Provider for ContentRepository instance
final contentRepositoryProvider = Provider<ContentRepository>((ref) {
  return ContentRepository();
});

/// Fetches a random active tagline from the API.
/// Returns null if no active taglines exist or on error (widget uses fallback text).
final taglineProvider = FutureProvider<String?>((ref) async {
  final repo = ref.watch(contentRepositoryProvider);
  final result = await repo.fetchRandomTagline();
  return switch (result) {
    Success(:final data) => data,
    Failure() => null,
  };
});

/// Fetches the active hero video URL from the API.
/// Returns null if no active video exists (widget uses bundled asset as fallback).
final heroVideoUrlProvider = FutureProvider<String?>((ref) async {
  final repo = ref.watch(contentRepositoryProvider);
  final result = await repo.fetchActiveVideoUrl();
  return switch (result) {
    Success(:final data) => data,
    Failure() => null,
  };
});
