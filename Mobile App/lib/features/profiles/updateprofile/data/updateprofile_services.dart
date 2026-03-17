import 'dart:convert';
import 'package:http/http.dart' as http;
import '../../../../core/constants/api_constants.dart';
import '../../../auth/login/model/auth_model.dart';
import '../../../../core/utils/app_logger.dart';

class UpdateProfileService {

  Future<User> updateProfile({
    required int userId, 
    required String username,
    required String email,
    required String phoneNumber,
    required String? profilePhotoBase64,
    required String firstName,
    required String lastName,
  }) async {
    final url = Uri.parse(ApiConfig.updateprofileUrl.replaceFirst('{userId}', userId.toString()));

    final Map<String, String> requestBody = {
      'username': username,
      'email': email,
      'phone_number': phoneNumber,
      'profile_picture': profilePhotoBase64 ?? '',
      'first_name' : firstName,
      'last_name' : lastName,
    };

    AppLogger.i('Updating profile for userId: $userId, URL: $url', 'UPDATE-PROFILE');
    AppLogger.d('Request body: ${jsonEncode(requestBody)}', 'UPDATE-PROFILE');

    http.Response response;

    try {
      response = await http.put( 
        url,
        headers: {
          'x-api-key' : ApiConfig.apiKey,
          'Accept': 'application/json',
          'Content-Type': 'application/json', 
        },
        body: jsonEncode(requestBody), 
      );
    } catch (e) {
      AppLogger.e('Network error for userId: $userId', e, null, 'UPDATE-PROFILE');
      throw Exception('Tidak dapat terhubung ke server. Silakan coba lagi.');
    }

    AppLogger.api(
      serviceName: 'UPDATE-PROFILE',
      method: 'PUT',
      url: url.toString(),
      headers: {'x-api-key': ApiConfig.apiKey, 'Content-Type': 'application/json'},
      body: requestBody,
      statusCode: response.statusCode,
      response: response.body,
    );

    final statusCode = response.statusCode;
    dynamic body; 
    try {
      body = json.decode(response.body);
    } catch (e) {
      AppLogger.e('Failed to parse JSON response for userId: $userId', e, null, 'UPDATE-PROFILE');
      throw Exception('Format respons server tidak valid.');
    }

    switch (statusCode) {
      case 200:
        if (body['status'] == 'success' && body['data'] != null) {
          AppLogger.s('Profile update successful for userId: $userId', 'UPDATE-PROFILE');
          return User.fromJson(body['data']['user']);
        } else {
          AppLogger.w('Update failed - Status not success for userId: $userId', 'UPDATE-PROFILE');
          throw Exception(body['message'] ?? 'Pembaruan profil gagal.');
        }
      case 400:
        AppLogger.e('Error 400 - Bad Request for userId: $userId - ${body['message']}', null, null, 'UPDATE-PROFILE');
        throw Exception(body['message'] ?? 'Permintaan tidak valid (400)');
      case 401:
        AppLogger.e('Error 401 - Unauthorized for userId: $userId', null, null, 'UPDATE-PROFILE');
        throw Exception('Sesi Anda telah berakhir. Mohon login ulang (401).');
      case 403:
        AppLogger.e('Error 403 - Forbidden for userId: $userId', null, null, 'UPDATE-PROFILE');
        throw Exception('Akses ditolak (403).');
      case 404:
        AppLogger.e('Error 404 - Not Found for userId: $userId', null, null, 'UPDATE-PROFILE');
        throw Exception('Pengguna tidak ditemukan atau endpoint API salah (404).');
      case 422:
        final errors = body['errors'];
        if (errors != null && errors is Map) {
          final errorMessages = errors.values
              .map((e) => (e is List && e.isNotEmpty) ? e[0] : e.toString())
              .join(', ');
          AppLogger.e('Error 422 - Validation Failed for userId: $userId - $errorMessages', null, null, 'UPDATE-PROFILE');
          throw Exception(errorMessages);
        }
        AppLogger.e('Error 422 - Unprocessable Entity for userId: $userId', null, null, 'UPDATE-PROFILE');
        throw Exception(body['message'] ?? 'Data yang dikirim tidak valid (422).');
      case 500:
        AppLogger.e('Error 500 - Internal Server Error for userId: $userId', null, null, 'UPDATE-PROFILE');
        throw Exception('Terjadi kesalahan server internal (500).');
      default:
        AppLogger.e('Unexpected Error - Status: $statusCode for userId: $userId', null, null, 'UPDATE-PROFILE');
        throw Exception('Terjadi kesalahan tidak terduga (${statusCode}): ${body['message'] ?? 'Tidak diketahui'}.');
    }
  }
}