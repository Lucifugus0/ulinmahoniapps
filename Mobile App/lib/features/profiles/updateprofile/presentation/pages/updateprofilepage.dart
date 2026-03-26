import 'package:flutter/material.dart';
import 'package:ulinmahoniapps/core/widgets/appbar.dart';
import '../../../../../core/layout/mainlayout.dart';
import '../../../../auth/login/provider/auth_provider.dart';
import '../../../../auth/login/model/auth_model.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../provider/updateprofile_provider.dart';
import '../../../../../core/widgets/dialog/errordialog.dart';
import '../../../../../core/widgets/dialog/notificationdialog.dart';
import '../../../../../core/widgets/dialog/imagesourcedialog.dart';
import '../../../../../core/constants/appcolor_constants.dart';
import 'package:image_picker/image_picker.dart';
import 'dart:convert';
import 'dart:typed_data';
import 'package:intl_phone_field/intl_phone_field.dart';
import 'package:intl_phone_field/countries.dart';
import 'package:go_router/go_router.dart';
import 'package:ulinmahoniapps/core/widgets/biometric_auth.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../../../../../core/utils/app_logger.dart';

Widget inputField(String hint, TextEditingController controller, {String? Function(String?)? validator, TextInputType? keyboardType, required bool isDark}) {
  return Container(
    decoration: BoxDecoration(
      color: isDark ? const Color(0xFF374151) : Colors.grey[100],
      borderRadius: BorderRadius.circular(8),
    ),
    child: Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16.0),
      child: TextFormField(
        controller: controller,
        keyboardType: keyboardType,
        style: TextStyle(color: isDark ? Colors.white : Colors.black87),
        decoration: InputDecoration(
          border: InputBorder.none,
          enabledBorder: InputBorder.none,
          focusedBorder: InputBorder.none,
          filled: false,
          hintText: hint,
          labelText: hint,
          labelStyle: TextStyle(color: isDark ? Colors.grey[400] : Colors.grey[600]),
          hintStyle: TextStyle(color: isDark ? Colors.grey[500] : Colors.grey[400]),
        ),
        validator: validator,
      ),
    ),
  );
}

class UpdateProfile extends ConsumerStatefulWidget {
  const UpdateProfile({super.key});

  @override
  ConsumerState<UpdateProfile> createState() => _UpdateProfileState();
}

class _UpdateProfileState extends ConsumerState<UpdateProfile> {
  final TextEditingController _NameController = TextEditingController();
  final TextEditingController _emailController = TextEditingController();
  final TextEditingController _phoneNumberController = TextEditingController();
  final TextEditingController _firstNameController = TextEditingController();
  final TextEditingController _lastNameController = TextEditingController();
  final BiometricAuthService _biometricAuthService = BiometricAuthService();

  final ImagePicker _picker = ImagePicker();
  Uint8List? _selectedImageBytes;
  String? _pickedImageBase64;
  String? _fullPhoneNumber;
  String _currentCountryCode = 'ID';
  int _phoneNumberLength = 0;

  @override
  void initState() {
    super.initState();
    _initializeControllers();
    _phoneNumberController.addListener(_updatePhoneNumberLength);
  }

  void _updatePhoneNumberLength() {
    setState(() {
      _phoneNumberLength = _phoneNumberController.text.length;
    });
  }

  Future<void> _initializeControllers() async {
    final authState = ref.read(authProvider);
    final User? user = authState.user.value;
    if (user != null) {
      _NameController.text = user.username;
      _emailController.text = user.email;
      _firstNameController.text = user.firstName;
      _lastNameController.text = user.lastName;

      if (user.phoneNumber.isNotEmpty) {
        _fullPhoneNumber = user.phoneNumber;

        String rawPhoneNumber = user.phoneNumber;
        if (rawPhoneNumber.startsWith('+')) {
          rawPhoneNumber = rawPhoneNumber.substring(1);
        }
        Country? detectedCountry;
        String nationalNumber = '';

        final sortedCountries = List<Country>.from(countries);
        sortedCountries.sort((a, b) => b.dialCode.length.compareTo(a.dialCode.length));

        for (Country country in sortedCountries) {
          if (rawPhoneNumber.startsWith(country.dialCode)) {
            detectedCountry = country;
            nationalNumber = rawPhoneNumber.substring(country.dialCode.length);
            break;
          }
        }

        if (detectedCountry != null) {
          _currentCountryCode = detectedCountry.code;
          if (nationalNumber.startsWith('0')) {
            nationalNumber = nationalNumber.substring(1);
          }
          _phoneNumberController.text = nationalNumber;
        } else {
          _phoneNumberController.text = user.phoneNumber;
          _currentCountryCode = 'ID';
        }
      } else {
        _fullPhoneNumber = '';
        _phoneNumberController.text = '';
        _currentCountryCode = 'ID';
      }
      _updatePhoneNumberLength();
      _selectedImageBytes = null;
      _pickedImageBase64 = null;
    }
  }

  @override
  void dispose() {
    _NameController.dispose();
    _emailController.dispose();
    _phoneNumberController.removeListener(_updatePhoneNumberLength);
    _phoneNumberController.dispose();
    _firstNameController.dispose();
    _lastNameController.dispose();
    super.dispose();
  }

  Future<void> _pickImageFromGallery() async {
    final pickedFile = await _picker.pickImage(source: ImageSource.gallery);
    if (pickedFile != null) {
      final bytes = await pickedFile.readAsBytes();
      setState(() {
        _selectedImageBytes = bytes;
        _pickedImageBase64 = base64Encode(bytes);
      });
      AppLogger.d("✅ Gambar galeri dikonversi ke Base64 (substring): ${_pickedImageBase64!.substring(0, 50)}...", 'UPDATE-PROFILE');
    }
  }

  Future<void> _pickImageFromCamera() async {
    final pickedFile = await _picker.pickImage(source: ImageSource.camera);
    if (pickedFile != null) {
      final bytes = await pickedFile.readAsBytes();
      setState(() {
        _selectedImageBytes = bytes;
        _pickedImageBase64 = base64Encode(bytes);
      });
      AppLogger.d("✅ Gambar kamera dikonversi ke Base64 (substring): ${_pickedImageBase64!.substring(0, 50)}...", 'UPDATE-PROFILE');
    }
  }

  Future<void> _showImageSourceDialog(BuildContext context) async {
    final localizations = AppLocalizations.of(context)!;
    final result = await showImageSourceDialog(
      context,
      title: localizations.pickImageSourceTitle,
      cameraButtonText: localizations.cameraOption,
      galleryButtonText: localizations.galleryOption,
      cancelButtonText: localizations.cancelButton,
    );

    if (result == ImageSourceOption.camera) {
      await _pickImageFromCamera();
    } else if (result == ImageSourceOption.gallery) {
      await _pickImageFromGallery();
    }
  }

  Future<void> _onRefresh() async {
    
    await ref.read(authProvider.notifier).refreshUserProfile();

    
    if (!mounted) return; 

    final authState = ref.read(authProvider);
    final error = authState.user.error;

    
    
    if (authState.user.hasError && error != null) {
      if (!error.toString().contains('Unauthorized')) {
        showNotificationDialog(
          context,
          'Gagal memperbarui profil: ${error.toString()}',
          defaultIcon: Icons.error_outline,
          iconColor: Colors.red,
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;
    final authState = ref.watch(authProvider);
    final user = authState.user.value;
    final updateProfileState = ref.watch(updateProfileNotifierProvider);
    final isDark = Theme.of(context).brightness == Brightness.dark;

    ref.listen<UpdateProfileState>(
      updateProfileNotifierProvider,
          (previous, current) {
        if (current is UpdateProfileSuccess) {
          showNotificationDialog(
            context,
            localizations.updateProfileSuccessMessage,
            // Use primaryAdaptive for dark/light mode compatibility
            iconColor: AppColors.primaryAdaptive(context),
            defaultIcon: Icons.check_circle_outline,
          );
          _initializeControllers();
        } else if (current is UpdateProfileError) {
          showErrorDialog(context, current.message);
        }
      },
    );
    return MainLayout(
      currentIndex: 4,
      showBottomNav: true,
      showNavBar: true,
      child: SafeArea(
        bottom: false,
        child: Column( 
          children: [
            CustomAppBar(title: localizations.updateProfileTitle, showBackButton: true),
            Expanded(
              child: RefreshIndicator(
                onRefresh: _onRefresh,
                // Use primaryAdaptive for dark/light mode compatibility
                color: AppColors.primaryAdaptive(context),
                child: SingleChildScrollView(
                  physics: const AlwaysScrollableScrollPhysics(),
                  child: Column(
                    children: [
                      Card(
                        margin: const EdgeInsets.only(
                          left: 24,
                          right: 24,
                          bottom: 140,
                          top: 24,
                        ),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                        elevation: 4,
                        color: isDark ? const Color(0xFF1F2937) : Colors.white,
                        child: Padding(
                          padding: const EdgeInsets.all(16.0),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text("ID Card | KITAS | Passport", style: const TextStyle(fontSize: 14)),
                              const SizedBox(height: 6),
                              GestureDetector(
                                onTap: () => _showImageSourceDialog(context),
                                child: Container(
                                  width: double.infinity,
                                  height: 200,
                                  decoration: BoxDecoration(
                                    color: isDark ? const Color(0xFF374151) : Colors.grey[100],
                                    borderRadius: BorderRadius.circular(8),
                                    border: Border.all(color: isDark ? Colors.grey[600]! : Colors.grey[400]!),
                                  ),
                                  child: ClipRRect(
                                    borderRadius: BorderRadius.circular(10),
                                    child: _selectedImageBytes != null
                                        ? Image.memory(
                                      _selectedImageBytes!,
                                      fit: BoxFit.cover,
                                    )
                                        : (user != null && user.profilePhotoUrl.isNotEmpty
                                        ? Image.network(
                                      user.profilePhotoUrl,
                                      fit: BoxFit.cover,
                                      errorBuilder: (context, error, stackTrace) {
                                        return const Center(
                                            child: Icon(Icons.image_not_supported, size: 50, color: Colors.grey));
                                      },
                                    )
                                        : Center(
                                      child: Column(
                                        mainAxisAlignment: MainAxisAlignment.center,
                                        children: [
                                          const Icon(Icons.person, size: 60, color: Colors.grey),
                                          const SizedBox(height: 8),
                                          Text(localizations.uploadProfilePhotoPrompt,
                                              textAlign: TextAlign.center,
                                              style: const TextStyle(color: Colors.grey)),
                                        ],
                                      ),
                                    )),
                                  ),
                                ),
                              ),
                              const SizedBox(height: 16),
                              Text(localizations.firstNameLabel, style: const TextStyle(fontSize: 14)),
                              const SizedBox(height: 6),
                              inputField(localizations.firstNameHint, _firstNameController, isDark: isDark),
                              const SizedBox(height: 12),
                              Text(localizations.lastNameLabel, style: const TextStyle(fontSize: 14)),
                              const SizedBox(height: 6),
                              inputField(localizations.lastNameHint, _lastNameController, isDark: isDark),
                              const SizedBox(height: 12),
                              // Text(localizations.usernameLabel, style: const TextStyle(fontSize: 14)),
                              // const SizedBox(height: 6),
                              // inputField(localizations.usernameHint, _NameController),
                              // const SizedBox(height: 12),
                              Text(localizations.emailLabel, style: const TextStyle(fontSize: 14)),
                              const SizedBox(height: 6),
                              inputField(localizations.emailHint, _emailController, isDark: isDark),
                              const SizedBox(height: 12),
                              Text(localizations.phoneNumberLabel, style: const TextStyle(fontSize: 14)),
                              const SizedBox(height: 6),
                              Container(
                                decoration: BoxDecoration(
                                  color: isDark ? const Color(0xFF374151) : Colors.grey[100],
                                  borderRadius: BorderRadius.circular(8),
                                ),
                                child: IntlPhoneField(
                                  key: ValueKey(_currentCountryCode),
                                  controller: _phoneNumberController,
                                  decoration: InputDecoration(
                                    border: OutlineInputBorder(
                                      borderSide: BorderSide.none,
                                      borderRadius: BorderRadius.circular(8),
                                    ),
                                    filled: false,
                                    contentPadding: const EdgeInsets.symmetric(vertical: 12, horizontal: 16),
                                    counterText: "",
                                  ),
                                  initialCountryCode: _currentCountryCode,
                                  languageCode: "id",
                                  // Input text color adapts to dark/light mode
                                  style: TextStyle(color: isDark ? Colors.white : Colors.black87),
                                  dropdownIcon: Icon(Icons.arrow_drop_down, color: isDark ? Colors.white : Colors.black87),
                                  dropdownIconPosition: IconPosition.trailing,
                                  dropdownTextStyle: TextStyle(fontSize: 16, color: isDark ? Colors.white : Colors.black),
                                  // Use primaryAdaptive for dark/light mode compatibility
                                  cursorColor: AppColors.primaryAdaptive(context),
                                  onChanged: (phone) {
                                    _fullPhoneNumber = phone.completeNumber;
                                    _updatePhoneNumberLength();
                                  },
                                  onCountryChanged: (country) {
                                    setState(() {
                                      _currentCountryCode = country.code;
                                      _phoneNumberController.clear();
                                      _fullPhoneNumber = null;
                                    });
                                  },
                                ),
                              ),
                              Align(
                                alignment: Alignment.centerRight,
                                child: Text(
                                  '${_phoneNumberLength}/15',
                                  style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                                ),
                              ),
                              SizedBox(
                                height: 24,
                              ),
                              SizedBox(
                                width: double.infinity,
                                height: 52,
                                child: ElevatedButton(
                                  onPressed: updateProfileState is UpdateProfileLoading
                                      ? null
                                      : () async {
                                    final bool didAuthenticate = await _biometricAuthService.authenticateOnLoad(context);
                                    if (didAuthenticate) {
                                      final notifier = ref.read(updateProfileNotifierProvider.notifier);
                                      await notifier.updateProfile(
                                        username:_firstNameController.text.trim(),
                                        email: _emailController.text.trim(),
                                        phoneNumber: (_fullPhoneNumber ?? '').trim(),
                                        firstName: _firstNameController.text.trim(),
                                        lastName: _lastNameController.text.trim(),
                                        profilePhotoBase64: _pickedImageBase64,
                                      );
                                    } else {
                                      context.go('/profile');
                                    }
                                  },
                                  style: ElevatedButton.styleFrom(
                                    // Use primaryAdaptive for dark/light mode compatibility
                                    backgroundColor: AppColors.primaryAdaptive(context),
                                    foregroundColor: Colors.white,
                                    shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadius.circular(12),
                                    ),
                                  ),
                                  child: updateProfileState is UpdateProfileLoading
                                      ? const SizedBox(
                                    width: 24,
                                    height: 24,
                                    child: CircularProgressIndicator(
                                      valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                                    ),
                                  )
                                      : Text(localizations.updateProfileButton, style: const TextStyle(fontSize: 16)),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}