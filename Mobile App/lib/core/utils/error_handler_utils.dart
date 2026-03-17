import 'package:dio/dio.dart';
import 'dart:convert';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';

/// Utility class for handling errors and extracting user-friendly messages
class ErrorHandlerUtils {
  /// Extracts error message from DioException
  static String getDioErrorMessage(DioException dioError, AppLocalizations localizations) {
    String displayMessage = localizations.loginUnknownError;

    if (dioError.response?.statusCode == 401) {
      displayMessage = localizations.loginIncorrectCredentials;
    } else {
      if (dioError.response?.data != null) {
        try {
          final errorData = dioError.response?.data;
          if (errorData is String) {
            final decodedError = jsonDecode(errorData);
            displayMessage = decodedError['message'] ?? displayMessage;
          } else if (errorData is Map) {
            displayMessage = errorData['message'] ?? displayMessage;
          }
        } catch (e) {
          displayMessage = dioError.message ?? localizations.loginUnknownNetworkError;
        }
      } else {
        displayMessage = dioError.message ?? localizations.loginUnknownNetworkError;
      }
    }

    return displayMessage;
  }

  /// Extracts error message from generic exception
  static String getGenericErrorMessage(dynamic error, AppLocalizations localizations) {
    if (error is DioException) {
      return getDioErrorMessage(error, localizations);
    }
    return localizations.loginGeneralError(error.toString());
  }
}

