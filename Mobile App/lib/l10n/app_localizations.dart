import 'dart:async';

import 'package:flutter/foundation.dart';
import 'package:flutter/widgets.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:intl/intl.dart' as intl;

import 'app_localizations_en.dart';
import 'app_localizations_id.dart';
import 'app_localizations_zh.dart';

// ignore_for_file: type=lint

/// Callers can lookup localized strings with an instance of AppLocalizations
/// returned by `AppLocalizations.of(context)`.
///
/// Applications need to include `AppLocalizations.delegate()` in their app's
/// `localizationDelegates` list, and the locales they support in the app's
/// `supportedLocales` list. For example:
///
/// ```dart
/// import 'l10n/app_localizations.dart';
///
/// return MaterialApp(
///   localizationsDelegates: AppLocalizations.localizationsDelegates,
///   supportedLocales: AppLocalizations.supportedLocales,
///   home: MyApplicationHome(),
/// );
/// ```
///
/// ## Update pubspec.yaml
///
/// Please make sure to update your pubspec.yaml to include the following
/// packages:
///
/// ```yaml
/// dependencies:
///   # Internationalization support.
///   flutter_localizations:
///     sdk: flutter
///   intl: any # Use the pinned version from flutter_localizations
///
///   # Rest of dependencies
/// ```
///
/// ## iOS Applications
///
/// iOS applications define key application metadata, including supported
/// locales, in an Info.plist file that is built into the application bundle.
/// To configure the locales supported by your app, you’ll need to edit this
/// file.
///
/// First, open your project’s ios/Runner.xcworkspace Xcode workspace file.
/// Then, in the Project Navigator, open the Info.plist file under the Runner
/// project’s Runner folder.
///
/// Next, select the Information Property List item, select Add Item from the
/// Editor menu, then select Localizations from the pop-up menu.
///
/// Select and expand the newly-created Localizations item then, for each
/// locale your application supports, add a new item and select the locale
/// you wish to add from the pop-up menu in the Value field. This list should
/// be consistent with the languages listed in the AppLocalizations.supportedLocales
/// property.
abstract class AppLocalizations {
  AppLocalizations(String locale)
    : localeName = intl.Intl.canonicalizedLocale(locale.toString());

  final String localeName;

  static AppLocalizations? of(BuildContext context) {
    return Localizations.of<AppLocalizations>(context, AppLocalizations);
  }

  static const LocalizationsDelegate<AppLocalizations> delegate =
      _AppLocalizationsDelegate();

  /// A list of this localizations delegate along with the default localizations
  /// delegates.
  ///
  /// Returns a list of localizations delegates containing this delegate along with
  /// GlobalMaterialLocalizations.delegate, GlobalCupertinoLocalizations.delegate,
  /// and GlobalWidgetsLocalizations.delegate.
  ///
  /// Additional delegates can be added by appending to this list in
  /// MaterialApp. This list does not have to be used at all if a custom list
  /// of delegates is preferred or required.
  static const List<LocalizationsDelegate<dynamic>> localizationsDelegates =
      <LocalizationsDelegate<dynamic>>[
        delegate,
        GlobalMaterialLocalizations.delegate,
        GlobalCupertinoLocalizations.delegate,
        GlobalWidgetsLocalizations.delegate,
      ];

  /// A list of this localizations delegate's supported locales.
  static const List<Locale> supportedLocales = <Locale>[
    Locale('en'),
    Locale('id'),
    Locale('zh'),
  ];

  /// No description provided for @searchBannerTitle.
  ///
  /// In id, this message translates to:
  /// **'Cari'**
  String get searchBannerTitle;

  /// No description provided for @browseAll.
  ///
  /// In id, this message translates to:
  /// **'Semua Properti'**
  String get browseAll;

  /// No description provided for @categories.
  ///
  /// In id, this message translates to:
  /// **'Kategori'**
  String get categories;

  /// No description provided for @availableNow.
  ///
  /// In id, this message translates to:
  /// **'Tersedia Sekarang'**
  String get availableNow;

  /// No description provided for @filterCategoryAll.
  ///
  /// In id, this message translates to:
  /// **'Semua'**
  String get filterCategoryAll;

  /// No description provided for @popularArea.
  ///
  /// In id, this message translates to:
  /// **'Area Populer'**
  String get popularArea;

  /// No description provided for @budget.
  ///
  /// In id, this message translates to:
  /// **'Dekat Anda'**
  String get budget;

  /// No description provided for @promotion.
  ///
  /// In id, this message translates to:
  /// **'Promosi'**
  String get promotion;

  /// No description provided for @promoBanner.
  ///
  /// In id, this message translates to:
  /// **'Promo Spesial'**
  String get promoBanner;

  /// No description provided for @promoDetailTitle.
  ///
  /// In id, this message translates to:
  /// **'Detail Promo'**
  String get promoDetailTitle;

  /// No description provided for @promoDescription.
  ///
  /// In id, this message translates to:
  /// **'Deskripsi Promo'**
  String get promoDescription;

  /// No description provided for @promoCodeLabel.
  ///
  /// In id, this message translates to:
  /// **'Kode Promo'**
  String get promoCodeLabel;

  /// No description provided for @noDescription.
  ///
  /// In id, this message translates to:
  /// **'Tidak ada deskripsi'**
  String get noDescription;

  /// No description provided for @promoHowToClaim.
  ///
  /// In id, this message translates to:
  /// **'Cara Klaim'**
  String get promoHowToClaim;

  /// No description provided for @promoClaimStep1.
  ///
  /// In id, this message translates to:
  /// **'Pilih properti yang diinginkan'**
  String get promoClaimStep1;

  /// No description provided for @promoClaimStep2.
  ///
  /// In id, this message translates to:
  /// **'Lalu Pilih Kamar yang diinginkan'**
  String get promoClaimStep2;

  /// No description provided for @promoClaimStep3.
  ///
  /// In id, this message translates to:
  /// **'Masukkan Kode Voucher saat pemesanan'**
  String get promoClaimStep3;

  /// No description provided for @promoClaimStep4.
  ///
  /// In id, this message translates to:
  /// **'Lakukan Pemesanan'**
  String get promoClaimStep4;

  /// No description provided for @promoClaimStep5.
  ///
  /// In id, this message translates to:
  /// **'Nikmati promo/diskon yang didapatkan'**
  String get promoClaimStep5;

  /// No description provided for @promoTermsTitle.
  ///
  /// In id, this message translates to:
  /// **'Syarat & Ketentuan'**
  String get promoTermsTitle;

  /// No description provided for @promoTerm1.
  ///
  /// In id, this message translates to:
  /// **'Syarat dan ketentuan berlaku'**
  String get promoTerm1;

  /// No description provided for @promoTerm2.
  ///
  /// In id, this message translates to:
  /// **'Tidak dapat digabung dengan promo lain'**
  String get promoTerm2;

  /// No description provided for @promoTerm3.
  ///
  /// In id, this message translates to:
  /// **'Periode promo terbatas'**
  String get promoTerm3;

  /// No description provided for @promoTerm4.
  ///
  /// In id, this message translates to:
  /// **'Hanya berlaku untuk pengguna secara terbatas'**
  String get promoTerm4;

  /// No description provided for @promoLoadError.
  ///
  /// In id, this message translates to:
  /// **'Gagal memuat detail promo'**
  String get promoLoadError;

  /// No description provided for @homeLabel.
  ///
  /// In id, this message translates to:
  /// **'Beranda'**
  String get homeLabel;

  /// No description provided for @myBookingLabel.
  ///
  /// In id, this message translates to:
  /// **'Pesanan Saya'**
  String get myBookingLabel;

  /// No description provided for @umLabel.
  ///
  /// In id, this message translates to:
  /// **'UM'**
  String get umLabel;

  /// No description provided for @csLabel.
  ///
  /// In id, this message translates to:
  /// **'CS'**
  String get csLabel;

  /// No description provided for @profileLabel.
  ///
  /// In id, this message translates to:
  /// **'Profil'**
  String get profileLabel;

  /// No description provided for @popularAreaJakarta.
  ///
  /// In id, this message translates to:
  /// **'Jakarta'**
  String get popularAreaJakarta;

  /// No description provided for @detailJakarta.
  ///
  /// In id, this message translates to:
  /// **'Pusat bisnis dan hiburan metropolitan'**
  String get detailJakarta;

  /// No description provided for @popularAreaBogor.
  ///
  /// In id, this message translates to:
  /// **'Bogor'**
  String get popularAreaBogor;

  /// No description provided for @detailBogor.
  ///
  /// In id, this message translates to:
  /// **'Kota hujan dengan suasana sejuk dan asri'**
  String get detailBogor;

  /// No description provided for @welcomeTagline.
  ///
  /// In id, this message translates to:
  /// **'Temukan Tempat Menginap Sempurna Anda'**
  String get welcomeTagline;

  /// No description provided for @welcomeMessage.
  ///
  /// In id, this message translates to:
  /// **'Perjalanan Anda menuju kenyamanan dimulai di sini'**
  String get welcomeMessage;

  /// No description provided for @welcomeGetStartedButton.
  ///
  /// In id, this message translates to:
  /// **'Mulai'**
  String get welcomeGetStartedButton;

  /// No description provided for @welcomeSignUpPrompt.
  ///
  /// In id, this message translates to:
  /// **'Belum punya akun? '**
  String get welcomeSignUpPrompt;

  /// No description provided for @welcomeSignUpButton.
  ///
  /// In id, this message translates to:
  /// **'Daftar'**
  String get welcomeSignUpButton;

  /// No description provided for @filterCategoryKos.
  ///
  /// In id, this message translates to:
  /// **'Kos'**
  String get filterCategoryKos;

  /// No description provided for @filterCategoryApartment.
  ///
  /// In id, this message translates to:
  /// **'Apartemen'**
  String get filterCategoryApartment;

  /// No description provided for @filterCategoryHotel.
  ///
  /// In id, this message translates to:
  /// **'Hotel'**
  String get filterCategoryHotel;

  /// No description provided for @filterCategoryVilla.
  ///
  /// In id, this message translates to:
  /// **'Villa'**
  String get filterCategoryVilla;

  /// No description provided for @filterLabelCategory.
  ///
  /// In id, this message translates to:
  /// **'Kategori'**
  String get filterLabelCategory;

  /// No description provided for @filterLabelRentType.
  ///
  /// In id, this message translates to:
  /// **'Tipe Sewa'**
  String get filterLabelRentType;

  /// No description provided for @filterRentTypeDaily.
  ///
  /// In id, this message translates to:
  /// **'Harian'**
  String get filterRentTypeDaily;

  /// No description provided for @filterRentTypeMonthly.
  ///
  /// In id, this message translates to:
  /// **'Bulanan'**
  String get filterRentTypeMonthly;

  /// No description provided for @filterLabelCheckIn.
  ///
  /// In id, this message translates to:
  /// **'Tanggal Masuk'**
  String get filterLabelCheckIn;

  /// No description provided for @filterHintCheckIn.
  ///
  /// In id, this message translates to:
  /// **'Pilih tanggal'**
  String get filterHintCheckIn;

  /// No description provided for @filterLabelDuration.
  ///
  /// In id, this message translates to:
  /// **'Durasi'**
  String get filterLabelDuration;

  /// No description provided for @filterHintDurationDays.
  ///
  /// In id, this message translates to:
  /// **'Masukkan jumlah hari'**
  String get filterHintDurationDays;

  /// No description provided for @filterHintDurationMonths.
  ///
  /// In id, this message translates to:
  /// **'Masukkan jumlah bulan'**
  String get filterHintDurationMonths;

  /// No description provided for @filterSuffixDays.
  ///
  /// In id, this message translates to:
  /// **'Hari'**
  String get filterSuffixDays;

  /// No description provided for @filterSuffixMonths.
  ///
  /// In id, this message translates to:
  /// **'Bulan'**
  String get filterSuffixMonths;

  /// No description provided for @filterLabelCheckOut.
  ///
  /// In id, this message translates to:
  /// **'Tanggal Keluar'**
  String get filterLabelCheckOut;

  /// No description provided for @filterHintCheckOut.
  ///
  /// In id, this message translates to:
  /// **'Otomatis terisi'**
  String get filterHintCheckOut;

  /// No description provided for @filterButtonSearch.
  ///
  /// In id, this message translates to:
  /// **'Cari'**
  String get filterButtonSearch;

  /// No description provided for @loginButton.
  ///
  /// In id, this message translates to:
  /// **'Masuk'**
  String get loginButton;

  /// No description provided for @registerButton.
  ///
  /// In id, this message translates to:
  /// **'Daftar sekarang'**
  String get registerButton;

  /// No description provided for @forgotPasswordTitle.
  ///
  /// In id, this message translates to:
  /// **'Lupa Password'**
  String get forgotPasswordTitle;

  /// No description provided for @forgotPasswordSubtitle.
  ///
  /// In id, this message translates to:
  /// **'Tenang saja, cukup masukkan email kamu dan buat password baru'**
  String get forgotPasswordSubtitle;

  /// No description provided for @emailInputHint.
  ///
  /// In id, this message translates to:
  /// **'Masukkan Email'**
  String get emailInputHint;

  /// No description provided for @sendCodeButton.
  ///
  /// In id, this message translates to:
  /// **'Kirim Kode'**
  String get sendCodeButton;

  /// No description provided for @rememberPasswordPrompt.
  ///
  /// In id, this message translates to:
  /// **'Ingat Password?'**
  String get rememberPasswordPrompt;

  /// No description provided for @loginButtonText.
  ///
  /// In id, this message translates to:
  /// **'Masuk'**
  String get loginButtonText;

  /// No description provided for @emailEmptyError.
  ///
  /// In id, this message translates to:
  /// **'Email tidak boleh kosong'**
  String get emailEmptyError;

  /// No description provided for @emailInvalidError.
  ///
  /// In id, this message translates to:
  /// **'Format email tidak valid'**
  String get emailInvalidError;

  /// No description provided for @passwordResetSuccess.
  ///
  /// In id, this message translates to:
  /// **'Link reset password telah dikirim ke email Anda!'**
  String get passwordResetSuccess;

  /// No description provided for @passwordResetError.
  ///
  /// In id, this message translates to:
  /// **'Terjadi kesalahan saat mengirim permintaan.'**
  String get passwordResetError;

  /// No description provided for @availableStatus.
  ///
  /// In id, this message translates to:
  /// **'Tersedia'**
  String get availableStatus;

  /// No description provided for @unavailableStatus.
  ///
  /// In id, this message translates to:
  /// **'Tidak Tersedia'**
  String get unavailableStatus;

  /// No description provided for @unknownStatus.
  ///
  /// In id, this message translates to:
  /// **'Tidak Diketahui'**
  String get unknownStatus;

  /// No description provided for @startingFrom.
  ///
  /// In id, this message translates to:
  /// **'Mulai dari {price}/Bulan'**
  String startingFrom(Object price);

  /// No description provided for @loginWelcomeTitle.
  ///
  /// In id, this message translates to:
  /// **'Hello! Selamat datang kembali'**
  String get loginWelcomeTitle;

  /// No description provided for @loginEmailHint.
  ///
  /// In id, this message translates to:
  /// **'Masukkan email anda'**
  String get loginEmailHint;

  /// No description provided for @loginPasswordHint.
  ///
  /// In id, this message translates to:
  /// **'Masukkan password anda'**
  String get loginPasswordHint;

  /// No description provided for @loginEmptyError.
  ///
  /// In id, this message translates to:
  /// **'Beberapa kolom wajib tidak boleh kosong'**
  String get loginEmptyError;

  /// No description provided for @loginInvalidFormatError.
  ///
  /// In id, this message translates to:
  /// **'Alamat login yang Anda masukkan tidak valid. Harap gunakan format email atau nomor telepon yang benar.'**
  String get loginInvalidFormatError;

  /// No description provided for @loginPasswordLengthError.
  ///
  /// In id, this message translates to:
  /// **'Password Anda harus terdiri dari minimal 8 karakter.'**
  String get loginPasswordLengthError;

  /// No description provided for @loginIncorrectCredentials.
  ///
  /// In id, this message translates to:
  /// **'Nohp/email dan password harus sesuai'**
  String get loginIncorrectCredentials;

  /// No description provided for @loginUnknownError.
  ///
  /// In id, this message translates to:
  /// **'Terjadi kesalahan saat login.'**
  String get loginUnknownError;

  /// No description provided for @loginUnknownNetworkError.
  ///
  /// In id, this message translates to:
  /// **'Terjadi kesalahan jaringan yang tidak diketahui.'**
  String get loginUnknownNetworkError;

  /// No description provided for @loginGeneralError.
  ///
  /// In id, this message translates to:
  /// **'Terjadi kesalahan: {error}'**
  String loginGeneralError(Object error);

  /// No description provided for @loginOrText.
  ///
  /// In id, this message translates to:
  /// **'Atau'**
  String get loginOrText;

  /// No description provided for @loginNoAccountPrompt.
  ///
  /// In id, this message translates to:
  /// **'Tidak punya akun?'**
  String get loginNoAccountPrompt;

  /// No description provided for @loginAsGuestButton.
  ///
  /// In id, this message translates to:
  /// **'Masuk sebagai tamu'**
  String get loginAsGuestButton;

  /// No description provided for @rememberMe.
  ///
  /// In id, this message translates to:
  /// **'Ingat Saya'**
  String get rememberMe;

  /// No description provided for @biometricAuthFailed.
  ///
  /// In id, this message translates to:
  /// **'Otentikasi biometrik gagal. Silakan coba lagi.'**
  String get biometricAuthFailed;

  /// No description provided for @biometricAuthNotConfigured.
  ///
  /// In id, this message translates to:
  /// **'Otentikasi biometrik belum terkonfigurasi. Silakan masuk secara manual dan isi kolom remember me terlebih dahulu.'**
  String get biometricAuthNotConfigured;

  /// No description provided for @emailNotVerifiedTitle.
  ///
  /// In id, this message translates to:
  /// **'Email Belum Diverifikasi'**
  String get emailNotVerifiedTitle;

  /// No description provided for @emailNotVerifiedMessage.
  ///
  /// In id, this message translates to:
  /// **'Silakan verifikasi email Anda terlebih dahulu untuk melanjutkan. Cek inbox email Anda dan klik link verifikasi yang telah kami kirimkan.'**
  String get emailNotVerifiedMessage;

  /// No description provided for @accountDeactivatedTitle.
  ///
  /// In id, this message translates to:
  /// **'Akun Dinonaktifkan'**
  String get accountDeactivatedTitle;

  /// No description provided for @accountDeactivatedMessage.
  ///
  /// In id, this message translates to:
  /// **'Akun Anda telah dinonaktifkan. Silakan hubungi support untuk informasi lebih lanjut.'**
  String get accountDeactivatedMessage;

  /// No description provided for @registerWelcomeTitle.
  ///
  /// In id, this message translates to:
  /// **'Hello! Daftar untuk memulai'**
  String get registerWelcomeTitle;

  /// No description provided for @registerFormError.
  ///
  /// In id, this message translates to:
  /// **'Harap isi semua kolom yang wajib dengan benar.'**
  String get registerFormError;

  /// No description provided for @registerSuccessMessage.
  ///
  /// In id, this message translates to:
  /// **'Register Berhasil , Silahkan Verifikasi di email anda dan login kemballi'**
  String get registerSuccessMessage;

  /// No description provided for @registerUnknownError.
  ///
  /// In id, this message translates to:
  /// **'Terjadi kesalahan tidak dikenal saat registrasi.'**
  String get registerUnknownError;

  /// No description provided for @registersuccessnotif.
  ///
  /// In id, this message translates to:
  /// **'Register Berhasil , Silahkan Verifikasi di email anda dan login kemballi'**
  String get registersuccessnotif;

  /// No description provided for @firstNameLabel.
  ///
  /// In id, this message translates to:
  /// **'Nama Depan'**
  String get firstNameLabel;

  /// No description provided for @firstNameEmptyError.
  ///
  /// In id, this message translates to:
  /// **'Nama depan tidak boleh kosong'**
  String get firstNameEmptyError;

  /// No description provided for @lastNameLabel.
  ///
  /// In id, this message translates to:
  /// **'Nama Belakang'**
  String get lastNameLabel;

  /// No description provided for @lastNameEmptyError.
  ///
  /// In id, this message translates to:
  /// **'Nama belakang tidak boleh kosong'**
  String get lastNameEmptyError;

  /// No description provided for @registerOneNameLabel.
  ///
  /// In id, this message translates to:
  /// **'Saya hanya memiliki satu suku nama'**
  String get registerOneNameLabel;

  /// No description provided for @fullNameLabel.
  ///
  /// In id, this message translates to:
  /// **'Nama Lengkap'**
  String get fullNameLabel;

  /// No description provided for @nameEmptyError.
  ///
  /// In id, this message translates to:
  /// **'Nama Lengkap tidak boleh kosong'**
  String get nameEmptyError;

  /// No description provided for @usernameLabel.
  ///
  /// In id, this message translates to:
  /// **'Username'**
  String get usernameLabel;

  /// No description provided for @usernameEmptyError.
  ///
  /// In id, this message translates to:
  /// **'Username tidak boleh kosong'**
  String get usernameEmptyError;

  /// No description provided for @emailLabel.
  ///
  /// In id, this message translates to:
  /// **'Email'**
  String get emailLabel;

  /// No description provided for @phoneNumberLabel.
  ///
  /// In id, this message translates to:
  /// **'Nomor Telepon'**
  String get phoneNumberLabel;

  /// No description provided for @phoneNumberHint.
  ///
  /// In id, this message translates to:
  /// **'Masukkan Nomor Telepon'**
  String get phoneNumberHint;

  /// No description provided for @phoneNumberEmptyError.
  ///
  /// In id, this message translates to:
  /// **'Nomor telepon tidak boleh kosong'**
  String get phoneNumberEmptyError;

  /// No description provided for @passwordLabel.
  ///
  /// In id, this message translates to:
  /// **'Password'**
  String get passwordLabel;

  /// No description provided for @passwordEmptyError.
  ///
  /// In id, this message translates to:
  /// **'Password tidak boleh kosong'**
  String get passwordEmptyError;

  /// No description provided for @passwordLengthError.
  ///
  /// In id, this message translates to:
  /// **'Password harus minimal 8 karakter.'**
  String get passwordLengthError;

  /// No description provided for @passwordLengthInfo.
  ///
  /// In id, this message translates to:
  /// **'Password harus terdiri dari minimal 8 karakter.'**
  String get passwordLengthInfo;

  /// No description provided for @confirmPasswordLabel.
  ///
  /// In id, this message translates to:
  /// **'Konfirmasi password'**
  String get confirmPasswordLabel;

  /// No description provided for @confirmPasswordEmptyError.
  ///
  /// In id, this message translates to:
  /// **'Konfirmasi password tidak boleh kosong'**
  String get confirmPasswordEmptyError;

  /// No description provided for @passwordMismatchError.
  ///
  /// In id, this message translates to:
  /// **'Password dan konfirmasi password tidak sesuai.'**
  String get passwordMismatchError;

  /// No description provided for @confirmPasswordInfo.
  ///
  /// In id, this message translates to:
  /// **'Masukkan ulang password yang sama untuk konfirmasi.'**
  String get confirmPasswordInfo;

  /// No description provided for @registerOrText.
  ///
  /// In id, this message translates to:
  /// **'Atau daftar dengan'**
  String get registerOrText;

  /// No description provided for @registerHaveAccountPrompt.
  ///
  /// In id, this message translates to:
  /// **'Sudah punya akun?'**
  String get registerHaveAccountPrompt;

  /// No description provided for @updatePasswordTitle.
  ///
  /// In id, this message translates to:
  /// **'Perbarui Kata Sandi Anda'**
  String get updatePasswordTitle;

  /// No description provided for @updatePasswordSuccessMessage.
  ///
  /// In id, this message translates to:
  /// **'Password berhasil diperbarui!'**
  String get updatePasswordSuccessMessage;

  /// No description provided for @updatePasswordFormError.
  ///
  /// In id, this message translates to:
  /// **'Harap isi semua kolom dengan benar.'**
  String get updatePasswordFormError;

  /// No description provided for @updatePasswordUserIdError.
  ///
  /// In id, this message translates to:
  /// **'Kesalahan: ID pengguna tidak ditemukan. Silakan login ulang.'**
  String get updatePasswordUserIdError;

  /// No description provided for @oldPasswordLabel.
  ///
  /// In id, this message translates to:
  /// **'Kata Sandi Lama'**
  String get oldPasswordLabel;

  /// No description provided for @oldPasswordEmptyError.
  ///
  /// In id, this message translates to:
  /// **'Kata Sandi lama tidak boleh kosong'**
  String get oldPasswordEmptyError;

  /// No description provided for @newPasswordLabel.
  ///
  /// In id, this message translates to:
  /// **'Kata Sandi Baru'**
  String get newPasswordLabel;

  /// No description provided for @newPasswordEmptyError.
  ///
  /// In id, this message translates to:
  /// **'Kata Sandi baru tidak boleh kosong'**
  String get newPasswordEmptyError;

  /// No description provided for @newPasswordLengthError.
  ///
  /// In id, this message translates to:
  /// **'Kata Sandi harus minimal 8 karakter.'**
  String get newPasswordLengthError;

  /// No description provided for @newPasswordLengthInfo.
  ///
  /// In id, this message translates to:
  /// **'Kata Sandi baru harus terdiri dari minimal 8 karakter.'**
  String get newPasswordLengthInfo;

  /// No description provided for @confirmNewPasswordLabel.
  ///
  /// In id, this message translates to:
  /// **'Konfirmasi Kata Sandi Baru'**
  String get confirmNewPasswordLabel;

  /// No description provided for @confirmNewPasswordEmptyError.
  ///
  /// In id, this message translates to:
  /// **'Konfirmasi Kata Sandi baru tidak boleh kosong'**
  String get confirmNewPasswordEmptyError;

  /// No description provided for @newPasswordMismatchError.
  ///
  /// In id, this message translates to:
  /// **'Konfirmasi Kata Sandi tidak sesuai.'**
  String get newPasswordMismatchError;

  /// No description provided for @confirmNewPasswordInfo.
  ///
  /// In id, this message translates to:
  /// **'Masukkan ulang Kata Sandi baru yang sama untuk konfirmasi.'**
  String get confirmNewPasswordInfo;

  /// No description provided for @updatePasswordButton.
  ///
  /// In id, this message translates to:
  /// **'Perbarui Password'**
  String get updatePasswordButton;

  /// No description provided for @profileTitle.
  ///
  /// In id, this message translates to:
  /// **'Profil'**
  String get profileTitle;

  /// No description provided for @profileDefaultUsername.
  ///
  /// In id, this message translates to:
  /// **'Pengguna'**
  String get profileDefaultUsername;

  /// No description provided for @profileDefaultEmail.
  ///
  /// In id, this message translates to:
  /// **'pengguna@contoh.com'**
  String get profileDefaultEmail;

  /// No description provided for @profileWelcomeText.
  ///
  /// In id, this message translates to:
  /// **'Selamat Datang'**
  String get profileWelcomeText;

  /// No description provided for @profileUserProfileMenu.
  ///
  /// In id, this message translates to:
  /// **'Profil Pengguna'**
  String get profileUserProfileMenu;

  /// No description provided for @changePasswordTitle.
  ///
  /// In id, this message translates to:
  /// **'Ubah Password'**
  String get changePasswordTitle;

  /// No description provided for @changePasswordSubText.
  ///
  /// In id, this message translates to:
  /// **'Perbarui password akun Anda'**
  String get changePasswordSubText;

  /// No description provided for @profileContactUsMenu.
  ///
  /// In id, this message translates to:
  /// **'Hubungi Kami'**
  String get profileContactUsMenu;

  /// No description provided for @profileDeactivateAccountMenu.
  ///
  /// In id, this message translates to:
  /// **'Hapus Akun'**
  String get profileDeactivateAccountMenu;

  /// No description provided for @logoutTitle.
  ///
  /// In id, this message translates to:
  /// **'Keluar'**
  String get logoutTitle;

  /// No description provided for @logoutSubText.
  ///
  /// In id, this message translates to:
  /// **'Keluar dari akun'**
  String get logoutSubText;

  /// No description provided for @confirmLogoutMessage.
  ///
  /// In id, this message translates to:
  /// **'Anda yakin ingin keluar dari akun ini?'**
  String get confirmLogoutMessage;

  /// No description provided for @cancelButton.
  ///
  /// In id, this message translates to:
  /// **'Batal'**
  String get cancelButton;

  /// No description provided for @logoutButton.
  ///
  /// In id, this message translates to:
  /// **'Keluar'**
  String get logoutButton;

  /// No description provided for @logoutErrorMessage.
  ///
  /// In id, this message translates to:
  /// **'Gagal keluar'**
  String get logoutErrorMessage;

  /// No description provided for @additionalSectionTitle.
  ///
  /// In id, this message translates to:
  /// **'Tambahan'**
  String get additionalSectionTitle;

  /// No description provided for @helpCenterTitle.
  ///
  /// In id, this message translates to:
  /// **'Pusat Bantuan'**
  String get helpCenterTitle;

  /// No description provided for @aboutTitle.
  ///
  /// In id, this message translates to:
  /// **'Tentang'**
  String get aboutTitle;

  /// No description provided for @deactivateAccountTitle.
  ///
  /// In id, this message translates to:
  /// **'Hapus Akun'**
  String get deactivateAccountTitle;

  /// No description provided for @deactivateAccountSubText.
  ///
  /// In id, this message translates to:
  /// **'Hapus akun Anda '**
  String get deactivateAccountSubText;

  /// No description provided for @confirmDeactivateAccountMessage.
  ///
  /// In id, this message translates to:
  /// **'Apakah Anda yakin ingin menghapus akun Anda? Tindakan ini tidak dapat dibatalkan.'**
  String get confirmDeactivateAccountMessage;

  /// No description provided for @deactivateButton.
  ///
  /// In id, this message translates to:
  /// **'Hapus'**
  String get deactivateButton;

  /// No description provided for @deactivateSuccessMessage.
  ///
  /// In id, this message translates to:
  /// **'Akun berhasil dihapus.'**
  String get deactivateSuccessMessage;

  /// No description provided for @deactivateErrorMessage.
  ///
  /// In id, this message translates to:
  /// **'Gagal menonaktifkan akun.'**
  String get deactivateErrorMessage;

  /// No description provided for @deactivateUserIdNotFoundError.
  ///
  /// In id, this message translates to:
  /// **'ID Pengguna tidak ditemukan'**
  String get deactivateUserIdNotFoundError;

  /// No description provided for @deactivateAccountFailedError.
  ///
  /// In id, this message translates to:
  /// **'Gagal menonaktifkan akun'**
  String get deactivateAccountFailedError;

  /// No description provided for @deactivateAccountUnknownError.
  ///
  /// In id, this message translates to:
  /// **'Kesalahan tidak diketahui'**
  String get deactivateAccountUnknownError;

  /// No description provided for @updateProfileTitle.
  ///
  /// In id, this message translates to:
  /// **'Ubah Profil'**
  String get updateProfileTitle;

  /// No description provided for @updateProfileSuccessMessage.
  ///
  /// In id, this message translates to:
  /// **'Profil Berhasil Diperbarui'**
  String get updateProfileSuccessMessage;

  /// No description provided for @profileImageLoadError.
  ///
  /// In id, this message translates to:
  /// **'Gagal memuat gambar profil'**
  String get profileImageLoadError;

  /// No description provided for @uploadProfilePhotoPrompt.
  ///
  /// In id, this message translates to:
  /// **'Ketuk untuk unggah foto profil'**
  String get uploadProfilePhotoPrompt;

  /// No description provided for @pickImageSourceTitle.
  ///
  /// In id, this message translates to:
  /// **'Pilih Sumber Gambar'**
  String get pickImageSourceTitle;

  /// No description provided for @cameraOption.
  ///
  /// In id, this message translates to:
  /// **'Kamera'**
  String get cameraOption;

  /// No description provided for @galleryOption.
  ///
  /// In id, this message translates to:
  /// **'Galeri'**
  String get galleryOption;

  /// No description provided for @firstNameHint.
  ///
  /// In id, this message translates to:
  /// **'Nama Depan Anda'**
  String get firstNameHint;

  /// No description provided for @lastNameHint.
  ///
  /// In id, this message translates to:
  /// **'Nama Belakang Anda'**
  String get lastNameHint;

  /// No description provided for @usernameHint.
  ///
  /// In id, this message translates to:
  /// **'Nama Pengguna Anda'**
  String get usernameHint;

  /// No description provided for @emailHint.
  ///
  /// In id, this message translates to:
  /// **'Email Anda'**
  String get emailHint;

  /// No description provided for @updateProfileButton.
  ///
  /// In id, this message translates to:
  /// **'Perbarui Profil'**
  String get updateProfileButton;

  /// No description provided for @myBookingTitle.
  ///
  /// In id, this message translates to:
  /// **'Pemesanan Saya'**
  String get myBookingTitle;

  /// No description provided for @upcomingTab.
  ///
  /// In id, this message translates to:
  /// **'Mendatang'**
  String get upcomingTab;

  /// No description provided for @completedTab.
  ///
  /// In id, this message translates to:
  /// **'Selesai'**
  String get completedTab;

  /// No description provided for @errorLoadingData.
  ///
  /// In id, this message translates to:
  /// **'Kesalahan saat memuat data'**
  String get errorLoadingData;

  /// No description provided for @myBookingEmptyTitle.
  ///
  /// In id, this message translates to:
  /// **'Belum Ada Pesanan'**
  String get myBookingEmptyTitle;

  /// No description provided for @myBookingEmptyMessage.
  ///
  /// In id, this message translates to:
  /// **'Anda belum memiliki pesanan. Mulai jelajahi properti kami untuk menemukan tempat yang sempurna!'**
  String get myBookingEmptyMessage;

  /// No description provided for @myBookingBrowseProperties.
  ///
  /// In id, this message translates to:
  /// **'Jelajahi Properti'**
  String get myBookingBrowseProperties;

  /// No description provided for @csTitle.
  ///
  /// In id, this message translates to:
  /// **'Layanan Pelanggan'**
  String get csTitle;

  /// No description provided for @csNoBookings.
  ///
  /// In id, this message translates to:
  /// **'Tidak Ada Pesanan Aktif'**
  String get csNoBookings;

  /// No description provided for @csNoBookingsDesc.
  ///
  /// In id, this message translates to:
  /// **'Anda tidak memiliki pesanan aktif untuk dibicarakan. Pesan properti terlebih dahulu.'**
  String get csNoBookingsDesc;

  /// No description provided for @csSelectRecipient.
  ///
  /// In id, this message translates to:
  /// **'Pilih Penerima'**
  String get csSelectRecipient;

  /// No description provided for @csFrontOffice.
  ///
  /// In id, this message translates to:
  /// **'Front Office'**
  String get csFrontOffice;

  /// No description provided for @csHeadOffice.
  ///
  /// In id, this message translates to:
  /// **'Head Office Finance'**
  String get csHeadOffice;

  /// No description provided for @csHeadOfficeDesc.
  ///
  /// In id, this message translates to:
  /// **'Pertanyaan keuangan dan pembayaran'**
  String get csHeadOfficeDesc;

  /// No description provided for @backToHome.
  ///
  /// In id, this message translates to:
  /// **'Kembali ke Beranda'**
  String get backToHome;

  /// No description provided for @tryAgain.
  ///
  /// In id, this message translates to:
  /// **'Coba Lagi'**
  String get tryAgain;

  /// No description provided for @cancel.
  ///
  /// In id, this message translates to:
  /// **'Batal'**
  String get cancel;

  /// No description provided for @checkInLabel.
  ///
  /// In id, this message translates to:
  /// **'Tanggal Masuk'**
  String get checkInLabel;

  /// No description provided for @checkOutLabel.
  ///
  /// In id, this message translates to:
  /// **'Tanggal Keluar'**
  String get checkOutLabel;

  /// No description provided for @myBookingDetailSelectImageFirst.
  ///
  /// In id, this message translates to:
  /// **'ℹ️ Pilih gambar terlebih dahulu'**
  String get myBookingDetailSelectImageFirst;

  /// No description provided for @myBookingDetailUploadSuccess.
  ///
  /// In id, this message translates to:
  /// **'✅ Gambar berhasil diunggah'**
  String get myBookingDetailUploadSuccess;

  /// No description provided for @myBookingDetailUploadFailed.
  ///
  /// In id, this message translates to:
  /// **'❌ Gagal mengunggah gambar'**
  String get myBookingDetailUploadFailed;

  /// No description provided for @myBookingDetailDataNotFound.
  ///
  /// In id, this message translates to:
  /// **'Data pemesanan tidak ditemukan.'**
  String get myBookingDetailDataNotFound;

  /// No description provided for @myBookingDetailPropertyNameDefault.
  ///
  /// In id, this message translates to:
  /// **'Nama Kos'**
  String get myBookingDetailPropertyNameDefault;

  /// No description provided for @myBookingDetailRoomNameDefault.
  ///
  /// In id, this message translates to:
  /// **'Nama Kamar'**
  String get myBookingDetailRoomNameDefault;

  /// No description provided for @myBookingDetailPaymentProofWarningTitle.
  ///
  /// In id, this message translates to:
  /// **'Bukti pembayaran belum diunggah'**
  String get myBookingDetailPaymentProofWarningTitle;

  /// No description provided for @myBookingDetailPaymentProofWarningMessage.
  ///
  /// In id, this message translates to:
  /// **'Mohon unggah bukti pembayaran Anda untuk menghindari pembatalan pemesanan.'**
  String get myBookingDetailPaymentProofWarningMessage;

  /// No description provided for @myBookingDetailTitle.
  ///
  /// In id, this message translates to:
  /// **'Detail Pemesanan'**
  String get myBookingDetailTitle;

  /// No description provided for @myBookingDetailOrderId.
  ///
  /// In id, this message translates to:
  /// **'Id Pemesanan:'**
  String get myBookingDetailOrderId;

  /// No description provided for @myBookingDetailPhoneNumber.
  ///
  /// In id, this message translates to:
  /// **'Nomor Telepon:'**
  String get myBookingDetailPhoneNumber;

  /// No description provided for @myBookingDetailBookingType.
  ///
  /// In id, this message translates to:
  /// **'Tipe Pemesanan:'**
  String get myBookingDetailBookingType;

  /// No description provided for @myBookingDetailDuration.
  ///
  /// In id, this message translates to:
  /// **'Durasi:'**
  String get myBookingDetailDuration;

  /// No description provided for @myBookingDetailTimeTitle.
  ///
  /// In id, this message translates to:
  /// **'Detail Waktu'**
  String get myBookingDetailTimeTitle;

  /// No description provided for @myBookingDetailCheckIn.
  ///
  /// In id, this message translates to:
  /// **'Tanggal Masuk:'**
  String get myBookingDetailCheckIn;

  /// No description provided for @myBookingDetailCheckOut.
  ///
  /// In id, this message translates to:
  /// **'Tanggal Keluar:'**
  String get myBookingDetailCheckOut;

  /// No description provided for @myBookingDetailBookingPriceTitle.
  ///
  /// In id, this message translates to:
  /// **'Harga Booking'**
  String get myBookingDetailBookingPriceTitle;

  /// No description provided for @myBookingDetailPricePerDay.
  ///
  /// In id, this message translates to:
  /// **'Harga per hari:'**
  String get myBookingDetailPricePerDay;

  /// No description provided for @myBookingDetailPricePerMonth.
  ///
  /// In id, this message translates to:
  /// **'Harga per bulan:'**
  String get myBookingDetailPricePerMonth;

  /// No description provided for @myBookingDetailSubtotal.
  ///
  /// In id, this message translates to:
  /// **'Subtotal:'**
  String get myBookingDetailSubtotal;

  /// No description provided for @myBookingDetailSubtotalBeforeDiscount.
  ///
  /// In id, this message translates to:
  /// **'Subtotal Sebelum Diskon:'**
  String get myBookingDetailSubtotalBeforeDiscount;

  /// No description provided for @myBookingDetailVoucher.
  ///
  /// In id, this message translates to:
  /// **'Voucher'**
  String get myBookingDetailVoucher;

  /// No description provided for @myBookingDetailDeposit.
  ///
  /// In id, this message translates to:
  /// **'Deposit:'**
  String get myBookingDetailDeposit;

  /// No description provided for @myBookingDetailNumberOfDays.
  ///
  /// In id, this message translates to:
  /// **'Jumlah Malam:'**
  String get myBookingDetailNumberOfDays;

  /// No description provided for @myBookingDetailNumberOfMonths.
  ///
  /// In id, this message translates to:
  /// **'Jumlah Bulan:'**
  String get myBookingDetailNumberOfMonths;

  /// No description provided for @myBookingDetailTotalPriceTitle.
  ///
  /// In id, this message translates to:
  /// **'Total Harga'**
  String get myBookingDetailTotalPriceTitle;

  /// No description provided for @myBookingDetailServiceFee.
  ///
  /// In id, this message translates to:
  /// **'Biaya Layanan:'**
  String get myBookingDetailServiceFee;

  /// No description provided for @myBookingDetailGrandtotal.
  ///
  /// In id, this message translates to:
  /// **'Total Pembayaran:'**
  String get myBookingDetailGrandtotal;

  /// No description provided for @myBookingDetailPaymentProofTitle.
  ///
  /// In id, this message translates to:
  /// **'Bukti Pembayaran'**
  String get myBookingDetailPaymentProofTitle;

  /// No description provided for @myBookingDetailPickFromGallery.
  ///
  /// In id, this message translates to:
  /// **'Pilih dari Galeri'**
  String get myBookingDetailPickFromGallery;

  /// No description provided for @myBookingDetailTakePhoto.
  ///
  /// In id, this message translates to:
  /// **'Ambil Foto'**
  String get myBookingDetailTakePhoto;

  /// No description provided for @myBookingDetailUploading.
  ///
  /// In id, this message translates to:
  /// **'Mengunggah bukti pembayaran...'**
  String get myBookingDetailUploading;

  /// No description provided for @myBookingDetailUploadPaymentProof.
  ///
  /// In id, this message translates to:
  /// **'Unggah Bukti Pembayaran'**
  String get myBookingDetailUploadPaymentProof;

  /// No description provided for @myBookingDetailUploadThisImage.
  ///
  /// In id, this message translates to:
  /// **'Unggah Gambar Ini'**
  String get myBookingDetailUploadThisImage;

  /// No description provided for @myBookingDetailError.
  ///
  /// In id, this message translates to:
  /// **'Kesalahan'**
  String get myBookingDetailError;

  /// No description provided for @myBookingDetailAppBarTitle.
  ///
  /// In id, this message translates to:
  /// **'Detail Pemesanan Saya'**
  String get myBookingDetailAppBarTitle;

  /// No description provided for @cameraNotFoundError.
  ///
  /// In id, this message translates to:
  /// **'Tidak ada kamera yang ditemukan.'**
  String get cameraNotFoundError;

  /// No description provided for @cameraInitFailed.
  ///
  /// In id, this message translates to:
  /// **'Gagal menginisialisasi kamera'**
  String get cameraInitFailed;

  /// No description provided for @cameraUnexpectedError.
  ///
  /// In id, this message translates to:
  /// **'Terjadi kesalahan tak terduga'**
  String get cameraUnexpectedError;

  /// No description provided for @cameraSelectFirstError.
  ///
  /// In id, this message translates to:
  /// **'Kesalahan: Pilih kamera terlebih dahulu.'**
  String get cameraSelectFirstError;

  /// No description provided for @cameraCaptureFailed.
  ///
  /// In id, this message translates to:
  /// **'Gagal mengambil gambar'**
  String get cameraCaptureFailed;

  /// No description provided for @cameraPopupTitle.
  ///
  /// In id, this message translates to:
  /// **'Unggah Bukti Pembayaran'**
  String get cameraPopupTitle;

  /// No description provided for @cameraGalleryTooltip.
  ///
  /// In id, this message translates to:
  /// **'Pilih dari Galeri'**
  String get cameraGalleryTooltip;

  /// No description provided for @cameraTryAgain.
  ///
  /// In id, this message translates to:
  /// **'Coba Lagi'**
  String get cameraTryAgain;

  /// No description provided for @cameraGalleryButton.
  ///
  /// In id, this message translates to:
  /// **'Pilih dari Galeri'**
  String get cameraGalleryButton;

  /// No description provided for @cameraRetake.
  ///
  /// In id, this message translates to:
  /// **'Ambil Ulang'**
  String get cameraRetake;

  /// No description provided for @cameraUseThisImage.
  ///
  /// In id, this message translates to:
  /// **'Gunakan Gambar Ini'**
  String get cameraUseThisImage;

  /// No description provided for @viewerHideAttachment.
  ///
  /// In id, this message translates to:
  /// **'Sembunyikan Lampiran'**
  String get viewerHideAttachment;

  /// No description provided for @viewerShowPaymentProof.
  ///
  /// In id, this message translates to:
  /// **'Lihat Bukti Pembayaran'**
  String get viewerShowPaymentProof;

  /// No description provided for @viewerUpdatePaymentProof.
  ///
  /// In id, this message translates to:
  /// **'Perbarui Bukti Pembayaran'**
  String get viewerUpdatePaymentProof;

  /// No description provided for @propertyTypePageTitle.
  ///
  /// In id, this message translates to:
  /// **'Jelajahi Tipe Properti'**
  String get propertyTypePageTitle;

  /// No description provided for @propertyTypeNoActiveFound.
  ///
  /// In id, this message translates to:
  /// **'Tidak ada tipe properti aktif ditemukan.'**
  String get propertyTypeNoActiveFound;

  /// No description provided for @propertyTypeFailedToLoad.
  ///
  /// In id, this message translates to:
  /// **'Gagal memuat tipe properti'**
  String get propertyTypeFailedToLoad;

  /// No description provided for @searchResultTitle.
  ///
  /// In id, this message translates to:
  /// **'Hasil Pencarian'**
  String get searchResultTitle;

  /// No description provided for @searchResultFailedToLoad.
  ///
  /// In id, this message translates to:
  /// **'Gagal memuat hasil'**
  String get searchResultFailedToLoad;

  /// No description provided for @detailPropertyMonth.
  ///
  /// In id, this message translates to:
  /// **'Bulan'**
  String get detailPropertyMonth;

  /// No description provided for @detailPropertyError.
  ///
  /// In id, this message translates to:
  /// **'Kesalahan'**
  String get detailPropertyError;

  /// No description provided for @detailPropertyNameNotAvailable.
  ///
  /// In id, this message translates to:
  /// **'Nama Tidak Tersedia'**
  String get detailPropertyNameNotAvailable;

  /// No description provided for @detailPropertyTagNotAvailable.
  ///
  /// In id, this message translates to:
  /// **'Tag Tidak Tersedia'**
  String get detailPropertyTagNotAvailable;

  /// No description provided for @detailPropertyFloorCount.
  ///
  /// In id, this message translates to:
  /// **'{count, plural, one{1 Lantai} other{{count} Lantai}}'**
  String detailPropertyFloorCount(num count);

  /// No description provided for @detailPropertyFacilitiesTitle.
  ///
  /// In id, this message translates to:
  /// **'Fasilitas Properti'**
  String get detailPropertyFacilitiesTitle;

  /// No description provided for @detailPropertyNoFacilities.
  ///
  /// In id, this message translates to:
  /// **'Tidak ada fasilitas yang tersedia.'**
  String get detailPropertyNoFacilities;

  /// No description provided for @roomTypeAvailableRooms.
  ///
  /// In id, this message translates to:
  /// **'Ruangan Tersedia'**
  String get roomTypeAvailableRooms;

  /// No description provided for @roomTypeNoRoomsAvailable.
  ///
  /// In id, this message translates to:
  /// **'Tidak ada ruangan tersedia'**
  String get roomTypeNoRoomsAvailable;

  /// No description provided for @contactBarPriceLabel.
  ///
  /// In id, this message translates to:
  /// **'Harga Mulai Dari'**
  String get contactBarPriceLabel;

  /// No description provided for @contactBarSafetyLabel.
  ///
  /// In id, this message translates to:
  /// **'Promo'**
  String get contactBarSafetyLabel;

  /// No description provided for @contactBarLoginRequired.
  ///
  /// In id, this message translates to:
  /// **'Silakan login terlebih dahulu untuk melanjutkan pembayaran.'**
  String get contactBarLoginRequired;

  /// No description provided for @contactBarLoginButton.
  ///
  /// In id, this message translates to:
  /// **'Login'**
  String get contactBarLoginButton;

  /// No description provided for @contactBarProfileRequired.
  ///
  /// In id, this message translates to:
  /// **'Silakan isi data diri terlebih dahulu untuk melanjutkan pembayaran.'**
  String get contactBarProfileRequired;

  /// No description provided for @contactBarProfileButton.
  ///
  /// In id, this message translates to:
  /// **'Profil'**
  String get contactBarProfileButton;

  /// No description provided for @contactBarPhoneRequired.
  ///
  /// In id, this message translates to:
  /// **'Silakan tambahkan nomor telepon terlebih dahulu untuk melanjutkan pemesanan.'**
  String get contactBarPhoneRequired;

  /// No description provided for @contactBarPhoneButton.
  ///
  /// In id, this message translates to:
  /// **'Tambah Nomor'**
  String get contactBarPhoneButton;

  /// No description provided for @contactBarIncompleteOrder.
  ///
  /// In id, this message translates to:
  /// **'Harap lengkapi semua data pemesanan.'**
  String get contactBarIncompleteOrder;

  /// No description provided for @contactBarBookNowButton.
  ///
  /// In id, this message translates to:
  /// **'Pesan Sekarang'**
  String get contactBarBookNowButton;

  /// No description provided for @contactBarOtherPropertiesButton.
  ///
  /// In id, this message translates to:
  /// **'Properti Lainnya'**
  String get contactBarOtherPropertiesButton;

  /// No description provided for @roomDetailsError.
  ///
  /// In id, this message translates to:
  /// **'Kesalahan'**
  String get roomDetailsError;

  /// No description provided for @roomDetailsLoading.
  ///
  /// In id, this message translates to:
  /// **'Memuat detail ruangan...'**
  String get roomDetailsLoading;

  /// No description provided for @roomDetailsDaily.
  ///
  /// In id, this message translates to:
  /// **'Hari'**
  String get roomDetailsDaily;

  /// No description provided for @roomDetailsMonthly.
  ///
  /// In id, this message translates to:
  /// **'Bulan'**
  String get roomDetailsMonthly;

  /// No description provided for @roomDetailsCompleteForm.
  ///
  /// In id, this message translates to:
  /// **'Harap lengkapi semua data pemesanan.'**
  String get roomDetailsCompleteForm;

  /// No description provided for @roomDetailsPerMonth.
  ///
  /// In id, this message translates to:
  /// **'/Bulan'**
  String get roomDetailsPerMonth;

  /// No description provided for @roomDetailsPerDay.
  ///
  /// In id, this message translates to:
  /// **'/Hari'**
  String get roomDetailsPerDay;

  /// No description provided for @roomDetailsFloor.
  ///
  /// In id, this message translates to:
  /// **'Lantai '**
  String get roomDetailsFloor;

  /// No description provided for @roomDetailsArea.
  ///
  /// In id, this message translates to:
  /// **'Luas '**
  String get roomDetailsArea;

  /// No description provided for @roomDetailsCapacity.
  ///
  /// In id, this message translates to:
  /// **'Kapasitas '**
  String get roomDetailsCapacity;

  /// No description provided for @roomBookinginfoTitle.
  ///
  /// In id, this message translates to:
  /// **'Informasi Pemesanan'**
  String get roomBookinginfoTitle;

  /// No description provided for @roomDetailsCapacityCount.
  ///
  /// In id, this message translates to:
  /// **'{count, plural, one{1 Orang} other{{count} Orang}}'**
  String roomDetailsCapacityCount(num count);

  /// No description provided for @roomDetailsBed.
  ///
  /// In id, this message translates to:
  /// **'Kasur '**
  String get roomDetailsBed;

  /// No description provided for @roomDetailsFacilitiesTitle.
  ///
  /// In id, this message translates to:
  /// **'Fasilitas Ruangan'**
  String get roomDetailsFacilitiesTitle;

  /// No description provided for @roomDetailsNoFacilities.
  ///
  /// In id, this message translates to:
  /// **'Tidak ada fasilitas tersedia'**
  String get roomDetailsNoFacilities;

  /// No description provided for @roomDetailsCheckingAvailability.
  ///
  /// In id, this message translates to:
  /// **'Memeriksa ketersediaan...'**
  String get roomDetailsCheckingAvailability;

  /// No description provided for @roomDetailsFailedToCheckAvailability.
  ///
  /// In id, this message translates to:
  /// **'Gagal memeriksa ketersediaan.'**
  String get roomDetailsFailedToCheckAvailability;

  /// No description provided for @roomDetailsRoomAvailable.
  ///
  /// In id, this message translates to:
  /// **'Kamar tersedia'**
  String get roomDetailsRoomAvailable;

  /// No description provided for @roomDetailsRoomNotAvailable.
  ///
  /// In id, this message translates to:
  /// **'Kamar tidak tersedia di tanggal tersebut'**
  String get roomDetailsRoomNotAvailable;

  /// No description provided for @roomDetailsRoomStatusNotAvailable.
  ///
  /// In id, this message translates to:
  /// **'Ruangan Tidak Tersedia'**
  String get roomDetailsRoomStatusNotAvailable;

  /// No description provided for @roomDetailsRoomStatusUnderMaintenance.
  ///
  /// In id, this message translates to:
  /// **'Ruangan Dalam Perbaikan'**
  String get roomDetailsRoomStatusUnderMaintenance;

  /// No description provided for @roomDetailsRoomStatusCurrentlyRented.
  ///
  /// In id, this message translates to:
  /// **'Ruangan Sedang Disewa'**
  String get roomDetailsRoomStatusCurrentlyRented;

  /// No description provided for @roomDetailsRoomStatusOccupied.
  ///
  /// In id, this message translates to:
  /// **'Ruangan Sedang Terisi'**
  String get roomDetailsRoomStatusOccupied;

  /// No description provided for @roomDetailsRoomStatusCannotBook.
  ///
  /// In id, this message translates to:
  /// **'Ruangan Tidak Dapat Dipesan'**
  String get roomDetailsRoomStatusCannotBook;

  /// Peringatan di tombol pesan jika pengguna belum login
  ///
  /// In id, this message translates to:
  /// **'Anda harus login untuk memesan'**
  String get roomDetailsLoginRequired;

  /// Peringatan jika pengguna login tapi belum punya foto profil
  ///
  /// In id, this message translates to:
  /// **'Harap lengkapi Kartu Identitas Anda untuk memesan'**
  String get roomDetailsProfilePictureRequired;

  /// No description provided for @roomDetailsAdditionalFeesTitle.
  ///
  /// In id, this message translates to:
  /// **'Biaya Tambahan'**
  String get roomDetailsAdditionalFeesTitle;

  /// No description provided for @roomDetailsDepositFee.
  ///
  /// In id, this message translates to:
  /// **'Deposit'**
  String get roomDetailsDepositFee;

  /// No description provided for @roomDetailsDepositNote.
  ///
  /// In id, this message translates to:
  /// **'Deposit akan dikembalikan saat checkout'**
  String get roomDetailsDepositNote;

  /// No description provided for @roomDetailsParkingCar.
  ///
  /// In id, this message translates to:
  /// **'Parkir Mobil'**
  String get roomDetailsParkingCar;

  /// No description provided for @roomDetailsParkingMotorcycle.
  ///
  /// In id, this message translates to:
  /// **'Parkir Motor'**
  String get roomDetailsParkingMotorcycle;

  /// No description provided for @roomDetailsParkingOptional.
  ///
  /// In id, this message translates to:
  /// **'Opsional'**
  String get roomDetailsParkingOptional;

  /// No description provided for @renewBookingTitle.
  ///
  /// In id, this message translates to:
  /// **'Perpanjang Booking'**
  String get renewBookingTitle;

  /// No description provided for @renewBookingButton.
  ///
  /// In id, this message translates to:
  /// **'Perpanjang Booking'**
  String get renewBookingButton;

  /// No description provided for @renewBookingPeriodLabel.
  ///
  /// In id, this message translates to:
  /// **'Periode'**
  String get renewBookingPeriodLabel;

  /// No description provided for @renewBookingPeriodDaily.
  ///
  /// In id, this message translates to:
  /// **'Harian'**
  String get renewBookingPeriodDaily;

  /// No description provided for @renewBookingPeriodMonthly.
  ///
  /// In id, this message translates to:
  /// **'Bulanan'**
  String get renewBookingPeriodMonthly;

  /// No description provided for @renewBookingDurationLabel.
  ///
  /// In id, this message translates to:
  /// **'Durasi'**
  String get renewBookingDurationLabel;

  /// No description provided for @renewBookingDurationDay.
  ///
  /// In id, this message translates to:
  /// **'Hari'**
  String get renewBookingDurationDay;

  /// No description provided for @renewBookingDurationMonth.
  ///
  /// In id, this message translates to:
  /// **'Bulan'**
  String get renewBookingDurationMonth;

  /// No description provided for @renewBookingCheckInLabel.
  ///
  /// In id, this message translates to:
  /// **'Tanggal Check-in'**
  String get renewBookingCheckInLabel;

  /// No description provided for @renewBookingCheckOutLabel.
  ///
  /// In id, this message translates to:
  /// **'Tanggal Check-out'**
  String get renewBookingCheckOutLabel;

  /// No description provided for @renewBookingRoomAvailable.
  ///
  /// In id, this message translates to:
  /// **'Kamar tersedia'**
  String get renewBookingRoomAvailable;

  /// No description provided for @renewBookingRoomNotAvailable.
  ///
  /// In id, this message translates to:
  /// **'Kamar tidak tersedia untuk tanggal ini'**
  String get renewBookingRoomNotAvailable;

  /// No description provided for @renewBookingCheckingAvailability.
  ///
  /// In id, this message translates to:
  /// **'Memeriksa ketersediaan...'**
  String get renewBookingCheckingAvailability;

  /// No description provided for @renewBookingInfoMessage.
  ///
  /// In id, this message translates to:
  /// **'Voucher dan metode pembayaran dapat dipilih di halaman berikutnya'**
  String get renewBookingInfoMessage;

  /// No description provided for @renewBookingContinueButton.
  ///
  /// In id, this message translates to:
  /// **'Lanjutkan ke Pembayaran'**
  String get renewBookingContinueButton;

  /// No description provided for @renewBookingRoomNumber.
  ///
  /// In id, this message translates to:
  /// **'No. Kamar'**
  String get renewBookingRoomNumber;

  /// No description provided for @checkInDateLabel.
  ///
  /// In id, this message translates to:
  /// **'Tanggal Masuk'**
  String get checkInDateLabel;

  /// No description provided for @checkOutDateLabel.
  ///
  /// In id, this message translates to:
  /// **'Tanggal Keluar'**
  String get checkOutDateLabel;

  /// No description provided for @selectDateHint.
  ///
  /// In id, this message translates to:
  /// **'Pilih tanggal'**
  String get selectDateHint;

  /// No description provided for @autoFilledHint.
  ///
  /// In id, this message translates to:
  /// **'Otomatis terisi'**
  String get autoFilledHint;

  /// No description provided for @rentTypeLabel.
  ///
  /// In id, this message translates to:
  /// **'Tipe Sewa'**
  String get rentTypeLabel;

  /// No description provided for @dailyRentType.
  ///
  /// In id, this message translates to:
  /// **'Harian'**
  String get dailyRentType;

  /// No description provided for @monthlyRentType.
  ///
  /// In id, this message translates to:
  /// **'Bulanan'**
  String get monthlyRentType;

  /// No description provided for @durationLabel.
  ///
  /// In id, this message translates to:
  /// **'Durasi'**
  String get durationLabel;

  /// No description provided for @dailyDurationLabel.
  ///
  /// In id, this message translates to:
  /// **'Durasi (Hari)'**
  String get dailyDurationLabel;

  /// No description provided for @dailyDurationHint.
  ///
  /// In id, this message translates to:
  /// **'Masukkan hari'**
  String get dailyDurationHint;

  /// No description provided for @monthlyDurationLabel.
  ///
  /// In id, this message translates to:
  /// **'Durasi (Bulan)'**
  String get monthlyDurationLabel;

  /// No description provided for @monthlyDurationHint.
  ///
  /// In id, this message translates to:
  /// **'Masukkan bulan'**
  String get monthlyDurationHint;

  /// No description provided for @durationHint.
  ///
  /// In id, this message translates to:
  /// **'Pilih durasi'**
  String get durationHint;

  /// No description provided for @roomCardAvailable.
  ///
  /// In id, this message translates to:
  /// **'Tersedia'**
  String get roomCardAvailable;

  /// No description provided for @roomCardNotAvailable.
  ///
  /// In id, this message translates to:
  /// **'Tidak Tersedia'**
  String get roomCardNotAvailable;

  /// No description provided for @roomCardUnderMaintenance.
  ///
  /// In id, this message translates to:
  /// **'Dalam Perbaikan'**
  String get roomCardUnderMaintenance;

  /// No description provided for @roomCardCurrentlyRented.
  ///
  /// In id, this message translates to:
  /// **'Sedang Disewa'**
  String get roomCardCurrentlyRented;

  /// No description provided for @roomCardUnknown.
  ///
  /// In id, this message translates to:
  /// **'Tidak diketahui'**
  String get roomCardUnknown;

  /// No description provided for @roomCardStartingPrice.
  ///
  /// In id, this message translates to:
  /// **'Mulai dari {price}/Bulan'**
  String roomCardStartingPrice(Object price);

  /// No description provided for @dialogTermsTitle.
  ///
  /// In id, this message translates to:
  /// **'SYARAT DAN KETENTUAN PENGGUNAAN'**
  String get dialogTermsTitle;

  /// No description provided for @dialogTermsAgree.
  ///
  /// In id, this message translates to:
  /// **'Saya menyetujui syarat dan ketentuan'**
  String get dialogTermsAgree;

  /// No description provided for @dialogTermsContinueButton.
  ///
  /// In id, this message translates to:
  /// **'Setuju dan Lanjutkan'**
  String get dialogTermsContinueButton;

  /// No description provided for @dialogPrivacyTitle.
  ///
  /// In id, this message translates to:
  /// **'KEBIJAKAN PRIVASI DAN PERLINDUNGAN DATA PRIBADI'**
  String get dialogPrivacyTitle;

  /// No description provided for @dialogPrivacyAgree.
  ///
  /// In id, this message translates to:
  /// **'Saya memahami dan menyetujui Kebijakan Privasi'**
  String get dialogPrivacyAgree;

  /// No description provided for @paymentBookingSuccess.
  ///
  /// In id, this message translates to:
  /// **'Booking berhasil'**
  String get paymentBookingSuccess;

  /// No description provided for @paymentBookingFailed.
  ///
  /// In id, this message translates to:
  /// **'Gagal booking'**
  String get paymentBookingFailed;

  /// No description provided for @paymentError.
  ///
  /// In id, this message translates to:
  /// **'Error:'**
  String get paymentError;

  /// No description provided for @paymentRentType.
  ///
  /// In id, this message translates to:
  /// **'Tipe Sewa'**
  String get paymentRentType;

  /// No description provided for @paymentDuration.
  ///
  /// In id, this message translates to:
  /// **'Durasi'**
  String get paymentDuration;

  /// No description provided for @paymentDurationValue.
  ///
  /// In id, this message translates to:
  /// **'{count} {rentType, select, daily{Hari} monthly{Bulan} other{Hari}}'**
  String paymentDurationValue(num count, String rentType);

  /// No description provided for @paymentCheckInDate.
  ///
  /// In id, this message translates to:
  /// **'Tanggal Masuk'**
  String get paymentCheckInDate;

  /// No description provided for @paymentCheckOutDate.
  ///
  /// In id, this message translates to:
  /// **'Tanggal Keluar'**
  String get paymentCheckOutDate;

  /// No description provided for @paymentMethodTitle.
  ///
  /// In id, this message translates to:
  /// **'Metode Pembayaran'**
  String get paymentMethodTitle;

  /// No description provided for @paymentVoucherTitle.
  ///
  /// In id, this message translates to:
  /// **'Voucher'**
  String get paymentVoucherTitle;

  /// No description provided for @paymentVoucherPlaceholder.
  ///
  /// In id, this message translates to:
  /// **'Masukkan kode voucher'**
  String get paymentVoucherPlaceholder;

  /// No description provided for @paymentVoucherApplyButton.
  ///
  /// In id, this message translates to:
  /// **'Gunakan'**
  String get paymentVoucherApplyButton;

  /// No description provided for @paymentVoucherApplied.
  ///
  /// In id, this message translates to:
  /// **'Voucher berhasil digunakan'**
  String get paymentVoucherApplied;

  /// No description provided for @paymentVoucherInvalid.
  ///
  /// In id, this message translates to:
  /// **'Kode voucher tidak valid'**
  String get paymentVoucherInvalid;

  /// No description provided for @paymentVoucherMyVouchers.
  ///
  /// In id, this message translates to:
  /// **'Voucher Saya'**
  String get paymentVoucherMyVouchers;

  /// No description provided for @paymentVoucherRedeemCode.
  ///
  /// In id, this message translates to:
  /// **'Redeem Kode'**
  String get paymentVoucherRedeemCode;

  /// No description provided for @paymentVoucherNoVouchers.
  ///
  /// In id, this message translates to:
  /// **'Tidak ada voucher tersedia'**
  String get paymentVoucherNoVouchers;

  /// No description provided for @paymentVoucherUseButton.
  ///
  /// In id, this message translates to:
  /// **'Pakai'**
  String get paymentVoucherUseButton;

  /// No description provided for @paymentVoucherValidUntil.
  ///
  /// In id, this message translates to:
  /// **'Berlaku hingga'**
  String get paymentVoucherValidUntil;

  /// No description provided for @paymentVoucherRedeemTitle.
  ///
  /// In id, this message translates to:
  /// **'Masukkan Kode Voucher'**
  String get paymentVoucherRedeemTitle;

  /// No description provided for @paymentVoucherRedeemDescription.
  ///
  /// In id, this message translates to:
  /// **'Masukkan kode yang kamu terima untuk mendapat diskon'**
  String get paymentVoucherRedeemDescription;

  /// No description provided for @paymentVoucherRedeemHint.
  ///
  /// In id, this message translates to:
  /// **'contoh: PROMO2024'**
  String get paymentVoucherRedeemHint;

  /// No description provided for @paymentPageTitle.
  ///
  /// In id, this message translates to:
  /// **'Detail Pemesanan'**
  String get paymentPageTitle;

  /// No description provided for @paymentPriceDetails.
  ///
  /// In id, this message translates to:
  /// **'Rincian Harga'**
  String get paymentPriceDetails;

  /// No description provided for @paymentDailyPrice.
  ///
  /// In id, this message translates to:
  /// **'Harga Harian'**
  String get paymentDailyPrice;

  /// No description provided for @paymentMonthlyPrice.
  ///
  /// In id, this message translates to:
  /// **'Harga Bulanan'**
  String get paymentMonthlyPrice;

  /// No description provided for @paymentSubtotal.
  ///
  /// In id, this message translates to:
  /// **'Subtotal'**
  String get paymentSubtotal;

  /// No description provided for @paymentFee.
  ///
  /// In id, this message translates to:
  /// **'Biaya'**
  String get paymentFee;

  /// No description provided for @paymentTotalPrice.
  ///
  /// In id, this message translates to:
  /// **'Total Harga'**
  String get paymentTotalPrice;

  /// No description provided for @paymentBookNowButton.
  ///
  /// In id, this message translates to:
  /// **'Pesan Sekarang'**
  String get paymentBookNowButton;

  /// No description provided for @paymentGenerateTransferVA.
  ///
  /// In id, this message translates to:
  /// **'Generate Transfer VA'**
  String get paymentGenerateTransferVA;

  /// No description provided for @paymentVirtualAccountSelectBank.
  ///
  /// In id, this message translates to:
  /// **'Virtual Account - Pilih Bank'**
  String get paymentVirtualAccountSelectBank;

  /// No description provided for @paymentQRIS.
  ///
  /// In id, this message translates to:
  /// **'QRIS'**
  String get paymentQRIS;

  /// No description provided for @paymentQRISSubtitle.
  ///
  /// In id, this message translates to:
  /// **'Bayar dengan QR Code'**
  String get paymentQRISSubtitle;

  /// No description provided for @paymentCreditCard.
  ///
  /// In id, this message translates to:
  /// **'Kartu Kredit'**
  String get paymentCreditCard;

  /// No description provided for @paymentCreditCardSubtitle.
  ///
  /// In id, this message translates to:
  /// **'Visa, Mastercard, JCB'**
  String get paymentCreditCardSubtitle;

  /// No description provided for @paymentManualTransferBRI.
  ///
  /// In id, this message translates to:
  /// **'Transfer VA Manual Bank BRI'**
  String get paymentManualTransferBRI;

  /// No description provided for @paymentVehicleDetailTitle.
  ///
  /// In id, this message translates to:
  /// **'Detail Kendaraan'**
  String get paymentVehicleDetailTitle;

  /// No description provided for @paymentVehiclePlateLabel.
  ///
  /// In id, this message translates to:
  /// **'Nomor Plat Kendaraan *'**
  String get paymentVehiclePlateLabel;

  /// No description provided for @paymentVehiclePlateHint.
  ///
  /// In id, this message translates to:
  /// **'CONTOH: B 1234 ABC'**
  String get paymentVehiclePlateHint;

  /// No description provided for @paymentVehiclePlateHelper.
  ///
  /// In id, this message translates to:
  /// **'Wajib diisi untuk parkir kendaraan'**
  String get paymentVehiclePlateHelper;

  /// No description provided for @paymentWarningAgreeTerms.
  ///
  /// In id, this message translates to:
  /// **'Centang persetujuan syarat & ketentuan untuk melanjutkan'**
  String get paymentWarningAgreeTerms;

  /// No description provided for @paymentWarningSelectPayment.
  ///
  /// In id, this message translates to:
  /// **'Pilih metode pembayaran untuk melanjutkan'**
  String get paymentWarningSelectPayment;

  /// No description provided for @paymentWarningSelectBank.
  ///
  /// In id, this message translates to:
  /// **'Pilih bank untuk Virtual Account'**
  String get paymentWarningSelectBank;

  /// No description provided for @paymentWarningVehiclePlate.
  ///
  /// In id, this message translates to:
  /// **'Isi nomor plat kendaraan untuk parkir'**
  String get paymentWarningVehiclePlate;

  /// No description provided for @paymentWarningCompleteData.
  ///
  /// In id, this message translates to:
  /// **'Lengkapi semua data untuk melanjutkan'**
  String get paymentWarningCompleteData;

  /// No description provided for @paymentTermsAgreePrefix.
  ///
  /// In id, this message translates to:
  /// **'Saya menyetujui '**
  String get paymentTermsAgreePrefix;

  /// No description provided for @paymentTermsAnd.
  ///
  /// In id, this message translates to:
  /// **' serta '**
  String get paymentTermsAnd;

  /// No description provided for @paymentTermsConditions.
  ///
  /// In id, this message translates to:
  /// **'Syarat dan Ketentuan'**
  String get paymentTermsConditions;

  /// No description provided for @paymentPrivacyPolicy.
  ///
  /// In id, this message translates to:
  /// **'Kebijakan Privasi'**
  String get paymentPrivacyPolicy;

  /// No description provided for @vaGenerationFailedTitle.
  ///
  /// In id, this message translates to:
  /// **'Gagal Membuat VA'**
  String get vaGenerationFailedTitle;

  /// No description provided for @vaGenerationFailedMessage.
  ///
  /// In id, this message translates to:
  /// **'Booking berhasil disimpan, namun gagal membuat Virtual Account.'**
  String get vaGenerationFailedMessage;

  /// No description provided for @vaGenerationFailedNote.
  ///
  /// In id, this message translates to:
  /// **'Anda dapat mencoba membuat VA lagi dari halaman detail booking.'**
  String get vaGenerationFailedNote;

  /// No description provided for @vaGenerationErrorTitle.
  ///
  /// In id, this message translates to:
  /// **'Error Membuat VA'**
  String get vaGenerationErrorTitle;

  /// No description provided for @vaGenerationErrorMessage.
  ///
  /// In id, this message translates to:
  /// **'Booking berhasil disimpan, namun terjadi error saat membuat Virtual Account.'**
  String get vaGenerationErrorMessage;

  /// No description provided for @vaGenerationErrorNote.
  ///
  /// In id, this message translates to:
  /// **'Silakan hubungi customer service atau coba lagi nanti.'**
  String get vaGenerationErrorNote;

  /// No description provided for @qrisGenerationFailedTitle.
  ///
  /// In id, this message translates to:
  /// **'Gagal Membuat QRIS'**
  String get qrisGenerationFailedTitle;

  /// No description provided for @qrisGenerationFailedMessage.
  ///
  /// In id, this message translates to:
  /// **'Booking berhasil disimpan, namun gagal membuat QRIS.'**
  String get qrisGenerationFailedMessage;

  /// No description provided for @qrisGenerationFailedNote.
  ///
  /// In id, this message translates to:
  /// **'Anda dapat mencoba membuat QRIS lagi dari halaman detail booking.'**
  String get qrisGenerationFailedNote;

  /// No description provided for @qrisGenerationErrorTitle.
  ///
  /// In id, this message translates to:
  /// **'Error Membuat QRIS'**
  String get qrisGenerationErrorTitle;

  /// No description provided for @qrisGenerationErrorMessage.
  ///
  /// In id, this message translates to:
  /// **'Booking berhasil disimpan, namun terjadi error saat membuat QRIS.'**
  String get qrisGenerationErrorMessage;

  /// No description provided for @qrisGenerationErrorNote.
  ///
  /// In id, this message translates to:
  /// **'Silakan hubungi customer service atau coba lagi nanti.'**
  String get qrisGenerationErrorNote;

  /// No description provided for @ccGenerationFailedTitle.
  ///
  /// In id, this message translates to:
  /// **'Gagal Membuat Pembayaran CC'**
  String get ccGenerationFailedTitle;

  /// No description provided for @ccGenerationFailedMessage.
  ///
  /// In id, this message translates to:
  /// **'Booking berhasil disimpan, namun gagal membuat pembayaran Credit Card.'**
  String get ccGenerationFailedMessage;

  /// No description provided for @ccGenerationFailedNote.
  ///
  /// In id, this message translates to:
  /// **'Anda dapat mencoba membuat pembayaran lagi dari halaman detail booking.'**
  String get ccGenerationFailedNote;

  /// No description provided for @ccGenerationErrorTitle.
  ///
  /// In id, this message translates to:
  /// **'Error Pembayaran CC'**
  String get ccGenerationErrorTitle;

  /// No description provided for @ccGenerationErrorMessage.
  ///
  /// In id, this message translates to:
  /// **'Booking berhasil disimpan, namun terjadi error saat memproses pembayaran Credit Card.'**
  String get ccGenerationErrorMessage;

  /// No description provided for @ccGenerationErrorNote.
  ///
  /// In id, this message translates to:
  /// **'Silakan hubungi customer service atau coba lagi nanti.'**
  String get ccGenerationErrorNote;

  /// No description provided for @vaResultDialogTitle.
  ///
  /// In id, this message translates to:
  /// **'Virtual Account Berhasil Dibuat!'**
  String get vaResultDialogTitle;

  /// No description provided for @vaResultDialogBank.
  ///
  /// In id, this message translates to:
  /// **'Bank'**
  String get vaResultDialogBank;

  /// No description provided for @vaResultDialogVANumber.
  ///
  /// In id, this message translates to:
  /// **'Nomor VA'**
  String get vaResultDialogVANumber;

  /// No description provided for @vaResultDialogAmount.
  ///
  /// In id, this message translates to:
  /// **'Jumlah'**
  String get vaResultDialogAmount;

  /// No description provided for @vaResultDialogValidUntil.
  ///
  /// In id, this message translates to:
  /// **'Berlaku hingga'**
  String get vaResultDialogValidUntil;

  /// No description provided for @vaResultDialogHowToPayButton.
  ///
  /// In id, this message translates to:
  /// **'Lihat Cara Pembayaran'**
  String get vaResultDialogHowToPayButton;

  /// No description provided for @vaResultDialogOrderAgainButton.
  ///
  /// In id, this message translates to:
  /// **'Pesan Lagi'**
  String get vaResultDialogOrderAgainButton;

  /// No description provided for @vaResultDialogCloseButton.
  ///
  /// In id, this message translates to:
  /// **'Tutup'**
  String get vaResultDialogCloseButton;

  /// No description provided for @vaResultDialogCopySuccess.
  ///
  /// In id, this message translates to:
  /// **'Nomor VA berhasil disalin'**
  String get vaResultDialogCopySuccess;

  /// No description provided for @vaResultDialogLinkUnavailable.
  ///
  /// In id, this message translates to:
  /// **'Link cara pembayaran tidak tersedia'**
  String get vaResultDialogLinkUnavailable;

  /// No description provided for @vaResultDialogLinkError.
  ///
  /// In id, this message translates to:
  /// **'Tidak dapat membuka link cara pembayaran'**
  String get vaResultDialogLinkError;

  /// No description provided for @qrisResultDialogTitle.
  ///
  /// In id, this message translates to:
  /// **'QRIS Berhasil Dibuat!'**
  String get qrisResultDialogTitle;

  /// No description provided for @qrisResultDialogAmount.
  ///
  /// In id, this message translates to:
  /// **'Jumlah'**
  String get qrisResultDialogAmount;

  /// No description provided for @qrisResultDialogValidUntil.
  ///
  /// In id, this message translates to:
  /// **'Berlaku hingga'**
  String get qrisResultDialogValidUntil;

  /// No description provided for @qrisResultDialogScanQR.
  ///
  /// In id, this message translates to:
  /// **'Scan QR Code'**
  String get qrisResultDialogScanQR;

  /// No description provided for @qrisResultDialogDownloadQR.
  ///
  /// In id, this message translates to:
  /// **'Download QR Code'**
  String get qrisResultDialogDownloadQR;

  /// No description provided for @qrisResultDialogOrderAgainButton.
  ///
  /// In id, this message translates to:
  /// **'Pesan Lagi'**
  String get qrisResultDialogOrderAgainButton;

  /// No description provided for @qrisResultDialogCloseButton.
  ///
  /// In id, this message translates to:
  /// **'Tutup'**
  String get qrisResultDialogCloseButton;

  /// No description provided for @qrisResultDialogDownloadSuccess.
  ///
  /// In id, this message translates to:
  /// **'QR Code berhasil didownload'**
  String get qrisResultDialogDownloadSuccess;

  /// No description provided for @bookingDetailsParkingCar.
  ///
  /// In id, this message translates to:
  /// **'Parkir Mobil'**
  String get bookingDetailsParkingCar;

  /// No description provided for @bookingDetailsParkingMotorcycle.
  ///
  /// In id, this message translates to:
  /// **'Parkir Motor'**
  String get bookingDetailsParkingMotorcycle;

  /// No description provided for @bookingDetailsParkingDuration.
  ///
  /// In id, this message translates to:
  /// **'{count, plural, =1{1 Bulan} other{{count} Bulan}}'**
  String bookingDetailsParkingDuration(int count);

  /// No description provided for @qrisResultDialogDownloadError.
  ///
  /// In id, this message translates to:
  /// **'Gagal mendownload QR Code'**
  String get qrisResultDialogDownloadError;

  /// No description provided for @propertyDetailLocation.
  ///
  /// In id, this message translates to:
  /// **'Lokasi'**
  String get propertyDetailLocation;

  /// No description provided for @propertyDetailNearbyLocations.
  ///
  /// In id, this message translates to:
  /// **'Lokasi Terdekat'**
  String get propertyDetailNearbyLocations;

  /// No description provided for @roomFilterAllStatus.
  ///
  /// In id, this message translates to:
  /// **'Semua Status'**
  String get roomFilterAllStatus;

  /// No description provided for @roomFilterAvailable.
  ///
  /// In id, this message translates to:
  /// **'Tersedia'**
  String get roomFilterAvailable;

  /// No description provided for @roomFilterOccupied.
  ///
  /// In id, this message translates to:
  /// **'Terisi'**
  String get roomFilterOccupied;

  /// No description provided for @paymentAdditionalFees.
  ///
  /// In id, this message translates to:
  /// **'Biaya Tambahan'**
  String get paymentAdditionalFees;

  /// No description provided for @paymentDepositRequired.
  ///
  /// In id, this message translates to:
  /// **'Deposit (Wajib)'**
  String get paymentDepositRequired;

  /// No description provided for @paymentDepositNote.
  ///
  /// In id, this message translates to:
  /// **'Deposit akan dikembalikan saat checkout'**
  String get paymentDepositNote;

  /// No description provided for @paymentCarParking.
  ///
  /// In id, this message translates to:
  /// **'Parkir Mobil'**
  String get paymentCarParking;

  /// No description provided for @paymentMotorcycleParking.
  ///
  /// In id, this message translates to:
  /// **'Parkir Motor'**
  String get paymentMotorcycleParking;

  /// No description provided for @paymentParkingDurationTitle.
  ///
  /// In id, this message translates to:
  /// **'Jumlah Bulan Parkir'**
  String get paymentParkingDurationTitle;

  /// No description provided for @paymentTotalParkingFee.
  ///
  /// In id, this message translates to:
  /// **'Total Biaya Parkir:'**
  String get paymentTotalParkingFee;

  /// No description provided for @paymentSelectParking.
  ///
  /// In id, this message translates to:
  /// **'Pilih Parkir (Opsional)'**
  String get paymentSelectParking;

  /// No description provided for @paymentParkingFull.
  ///
  /// In id, this message translates to:
  /// **'Penuh ({available}/{capacity} tersedia)'**
  String paymentParkingFull(Object available, Object capacity);

  /// No description provided for @paymentParkingAvailable.
  ///
  /// In id, this message translates to:
  /// **'{available}/{capacity} tersedia'**
  String paymentParkingAvailable(Object available, Object capacity);

  /// No description provided for @paymentPerDay.
  ///
  /// In id, this message translates to:
  /// **'hari'**
  String get paymentPerDay;

  /// No description provided for @paymentPerMonth.
  ///
  /// In id, this message translates to:
  /// **'bulan'**
  String get paymentPerMonth;

  /// No description provided for @paymentMaxDuration.
  ///
  /// In id, this message translates to:
  /// **'maks: {duration} {unit}'**
  String paymentMaxDuration(Object duration, Object unit);

  /// No description provided for @confirmationDialogTitle.
  ///
  /// In id, this message translates to:
  /// **'Konfirmasi Pesanan'**
  String get confirmationDialogTitle;

  /// No description provided for @confirmationDialogBookingDetails.
  ///
  /// In id, this message translates to:
  /// **'Detail Pesanan'**
  String get confirmationDialogBookingDetails;

  /// No description provided for @confirmationDialogProperty.
  ///
  /// In id, this message translates to:
  /// **'Properti'**
  String get confirmationDialogProperty;

  /// No description provided for @confirmationDialogRoom.
  ///
  /// In id, this message translates to:
  /// **'Kamar'**
  String get confirmationDialogRoom;

  /// No description provided for @confirmationDialogRentType.
  ///
  /// In id, this message translates to:
  /// **'Tipe Sewa'**
  String get confirmationDialogRentType;

  /// No description provided for @confirmationDialogDuration.
  ///
  /// In id, this message translates to:
  /// **'Durasi'**
  String get confirmationDialogDuration;

  /// No description provided for @confirmationDialogCheckInDate.
  ///
  /// In id, this message translates to:
  /// **'Tanggal Masuk'**
  String get confirmationDialogCheckInDate;

  /// No description provided for @confirmationDialogCheckOutDate.
  ///
  /// In id, this message translates to:
  /// **'Tanggal Keluar'**
  String get confirmationDialogCheckOutDate;

  /// No description provided for @confirmationDialogPriceDetails.
  ///
  /// In id, this message translates to:
  /// **'Rincian Biaya'**
  String get confirmationDialogPriceDetails;

  /// No description provided for @confirmationDialogDailyPrice.
  ///
  /// In id, this message translates to:
  /// **'Harga Harian'**
  String get confirmationDialogDailyPrice;

  /// No description provided for @confirmationDialogMonthlyPrice.
  ///
  /// In id, this message translates to:
  /// **'Harga Bulanan'**
  String get confirmationDialogMonthlyPrice;

  /// No description provided for @confirmationDialogFee.
  ///
  /// In id, this message translates to:
  /// **'Biaya'**
  String get confirmationDialogFee;

  /// No description provided for @confirmationDialogTotalPrice.
  ///
  /// In id, this message translates to:
  /// **'Total Harga'**
  String get confirmationDialogTotalPrice;

  /// No description provided for @confirmationDialogConfirmationMessage.
  ///
  /// In id, this message translates to:
  /// **'Apakah Anda yakin dengan pesanan ini dan ingin melanjutkan pembayaran?'**
  String get confirmationDialogConfirmationMessage;

  /// No description provided for @confirmationDialogCancelButton.
  ///
  /// In id, this message translates to:
  /// **'Batal'**
  String get confirmationDialogCancelButton;

  /// No description provided for @confirmationDialogConfirmButton.
  ///
  /// In id, this message translates to:
  /// **'Setuju'**
  String get confirmationDialogConfirmButton;

  /// No description provided for @paymentMethodBankTransfer.
  ///
  /// In id, this message translates to:
  /// **'Transfer Bank'**
  String get paymentMethodBankTransfer;

  /// No description provided for @paymentMethodCash.
  ///
  /// In id, this message translates to:
  /// **'Bayar di Tempat'**
  String get paymentMethodCash;

  /// No description provided for @adminFeeLabel.
  ///
  /// In id, this message translates to:
  /// **'Biaya Admin'**
  String get adminFeeLabel;

  /// No description provided for @dailyDurationUnit.
  ///
  /// In id, this message translates to:
  /// **'Hari'**
  String get dailyDurationUnit;

  /// No description provided for @monthlyDurationUnit.
  ///
  /// In id, this message translates to:
  /// **'Bulan'**
  String get monthlyDurationUnit;

  /// No description provided for @normalPriceLabel.
  ///
  /// In id, this message translates to:
  /// **'(Harga Normal)'**
  String get normalPriceLabel;

  /// No description provided for @taxlabel.
  ///
  /// In id, this message translates to:
  /// **'Biaya Layanan'**
  String get taxlabel;

  /// No description provided for @errorDisplayTitle.
  ///
  /// In id, this message translates to:
  /// **'SEDANG MENGALAMI GANGGUAN'**
  String get errorDisplayTitle;

  /// No description provided for @contactUsTitle.
  ///
  /// In id, this message translates to:
  /// **'Hubungi Kami'**
  String get contactUsTitle;

  /// No description provided for @chatViaWhatsApp.
  ///
  /// In id, this message translates to:
  /// **'Chat melalui WhatsApp'**
  String get chatViaWhatsApp;

  /// No description provided for @sendEmail.
  ///
  /// In id, this message translates to:
  /// **'Kirim Email'**
  String get sendEmail;

  /// No description provided for @cancelButtonLabel.
  ///
  /// In id, this message translates to:
  /// **'Batal'**
  String get cancelButtonLabel;

  /// No description provided for @noInternetTitle.
  ///
  /// In id, this message translates to:
  /// **'Koneksi Terputus'**
  String get noInternetTitle;

  /// No description provided for @noInternetMessage.
  ///
  /// In id, this message translates to:
  /// **'Ups! Sepertinya Anda tidak terhubung ke internet. Mohon periksa koneksi Anda.'**
  String get noInternetMessage;

  /// No description provided for @retryButton.
  ///
  /// In id, this message translates to:
  /// **'OK'**
  String get retryButton;

  /// No description provided for @filtertitle.
  ///
  /// In id, this message translates to:
  /// **'Filter Pencarian'**
  String get filtertitle;

  /// No description provided for @checkInDialogTitle.
  ///
  /// In id, this message translates to:
  /// **'Konfirmasi Check-In'**
  String get checkInDialogTitle;

  /// No description provided for @checkInSectionTitle.
  ///
  /// In id, this message translates to:
  /// **'Check In'**
  String get checkInSectionTitle;

  /// No description provided for @checkInDialogIdCardSection.
  ///
  /// In id, this message translates to:
  /// **'Foto KTP'**
  String get checkInDialogIdCardSection;

  /// No description provided for @checkInDialogUploadIdCard.
  ///
  /// In id, this message translates to:
  /// **'Unggah KTP'**
  String get checkInDialogUploadIdCard;

  /// No description provided for @checkInDialogTapToUpload.
  ///
  /// In id, this message translates to:
  /// **'Ketuk untuk mengunggah dari kamera atau galeri'**
  String get checkInDialogTapToUpload;

  /// No description provided for @checkInDialogBookingDetails.
  ///
  /// In id, this message translates to:
  /// **'Detail Booking'**
  String get checkInDialogBookingDetails;

  /// No description provided for @checkInDialogPaymentProof.
  ///
  /// In id, this message translates to:
  /// **'Bukti Pembayaran'**
  String get checkInDialogPaymentProof;

  /// No description provided for @checkInDialogTermsAgreement.
  ///
  /// In id, this message translates to:
  /// **'Saya telah membaca dan menyetujui Syarat dan Ketentuan serta Kebijakan Privasi'**
  String get checkInDialogTermsAgreement;

  /// No description provided for @checkInDialogConfirmButton.
  ///
  /// In id, this message translates to:
  /// **'Konfirmasi Check-In'**
  String get checkInDialogConfirmButton;

  /// No description provided for @checkInDialogIdCardRequired.
  ///
  /// In id, this message translates to:
  /// **'Silakan unggah KTP Anda'**
  String get checkInDialogIdCardRequired;

  /// No description provided for @checkInDialogTermsRequired.
  ///
  /// In id, this message translates to:
  /// **'Silakan setujui syarat dan ketentuan'**
  String get checkInDialogTermsRequired;

  /// No description provided for @comingSoonTitle1.
  ///
  /// In id, this message translates to:
  /// **'SEGERA'**
  String get comingSoonTitle1;

  /// No description provided for @comingSoonTitle2.
  ///
  /// In id, this message translates to:
  /// **'HADIR'**
  String get comingSoonTitle2;

  /// No description provided for @comingSoonMessage.
  ///
  /// In id, this message translates to:
  /// **'Kami sedang bekerja keras untuk membuat\nsesuatu yang luar biasa, tunggu ya'**
  String get comingSoonMessage;

  /// No description provided for @profileAddressBookTitle.
  ///
  /// In id, this message translates to:
  /// **'Buku Alamat'**
  String get profileAddressBookTitle;

  /// No description provided for @profileAddressBookSubText.
  ///
  /// In id, this message translates to:
  /// **'Kelola alamat tersimpan Anda'**
  String get profileAddressBookSubText;

  /// No description provided for @profileOrderHistoryTitle.
  ///
  /// In id, this message translates to:
  /// **'Riwayat Pesanan'**
  String get profileOrderHistoryTitle;

  /// No description provided for @profileOrderHistorySubText.
  ///
  /// In id, this message translates to:
  /// **'Lihat pesanan sebelumnya'**
  String get profileOrderHistorySubText;

  /// No description provided for @profileLanguageTitle.
  ///
  /// In id, this message translates to:
  /// **'Bahasa'**
  String get profileLanguageTitle;

  /// No description provided for @profileLanguageSubText.
  ///
  /// In id, this message translates to:
  /// **'Indonesia'**
  String get profileLanguageSubText;

  /// No description provided for @profileNotificationsTitle.
  ///
  /// In id, this message translates to:
  /// **'Notifikasi'**
  String get profileNotificationsTitle;

  /// No description provided for @profileGetHelpTitle.
  ///
  /// In id, this message translates to:
  /// **'Bantuan'**
  String get profileGetHelpTitle;

  /// No description provided for @profilePrivacyPolicyTitle.
  ///
  /// In id, this message translates to:
  /// **'Kebijakan Privasi'**
  String get profilePrivacyPolicyTitle;

  /// No description provided for @profileTermsConditionsTitle.
  ///
  /// In id, this message translates to:
  /// **'Syarat & Ketentuan'**
  String get profileTermsConditionsTitle;

  /// No description provided for @roomSortAllRooms.
  ///
  /// In id, this message translates to:
  /// **'Semua Kamar'**
  String get roomSortAllRooms;

  /// No description provided for @roomSortFilterBy.
  ///
  /// In id, this message translates to:
  /// **'Filter berdasarkan'**
  String get roomSortFilterBy;

  /// No description provided for @profileIdMissingWarning.
  ///
  /// In id, this message translates to:
  /// **'Dokumen identitas belum diunggah'**
  String get profileIdMissingWarning;

  /// No description provided for @profileIdMissingDesc.
  ///
  /// In id, this message translates to:
  /// **'Unggah KTP, KITAS, atau Passport Anda untuk verifikasi'**
  String get profileIdMissingDesc;

  /// No description provided for @profilePhoneMissingWarning.
  ///
  /// In id, this message translates to:
  /// **'Nomor telepon belum ditambahkan'**
  String get profilePhoneMissingWarning;

  /// No description provided for @profilePhoneMissingDesc.
  ///
  /// In id, this message translates to:
  /// **'Tambahkan nomor telepon Anda untuk kemudahan komunikasi'**
  String get profilePhoneMissingDesc;

  /// No description provided for @profileUploadIdTitle.
  ///
  /// In id, this message translates to:
  /// **'Unggah Dokumen Identitas'**
  String get profileUploadIdTitle;

  /// No description provided for @profileUploadIdSubText.
  ///
  /// In id, this message translates to:
  /// **'KTP / KITAS / Passport'**
  String get profileUploadIdSubText;

  /// No description provided for @profileUploadIdInfo.
  ///
  /// In id, this message translates to:
  /// **'Mohon unggah foto yang jelas dari dokumen identitas Anda (KTP, KITAS, atau Passport) untuk verifikasi identitas'**
  String get profileUploadIdInfo;

  /// No description provided for @profileUploadIdNoImageSelected.
  ///
  /// In id, this message translates to:
  /// **'Belum ada gambar dipilih'**
  String get profileUploadIdNoImageSelected;

  /// No description provided for @profileUploadIdSelectImage.
  ///
  /// In id, this message translates to:
  /// **'Pilih Gambar'**
  String get profileUploadIdSelectImage;

  /// No description provided for @profileUploadIdChangeImage.
  ///
  /// In id, this message translates to:
  /// **'Ganti Gambar'**
  String get profileUploadIdChangeImage;

  /// No description provided for @profileUploadIdUpload.
  ///
  /// In id, this message translates to:
  /// **'Unggah Dokumen'**
  String get profileUploadIdUpload;

  /// No description provided for @profileUploadIdUploading.
  ///
  /// In id, this message translates to:
  /// **'Mengunggah...'**
  String get profileUploadIdUploading;

  /// No description provided for @profileUploadIdChooseSource.
  ///
  /// In id, this message translates to:
  /// **'Pilih Sumber Gambar'**
  String get profileUploadIdChooseSource;

  /// No description provided for @profileUploadIdCamera.
  ///
  /// In id, this message translates to:
  /// **'Kamera'**
  String get profileUploadIdCamera;

  /// No description provided for @profileUploadIdGallery.
  ///
  /// In id, this message translates to:
  /// **'Galeri'**
  String get profileUploadIdGallery;

  /// No description provided for @profileUploadIdSuccess.
  ///
  /// In id, this message translates to:
  /// **'Dokumen identitas berhasil diunggah'**
  String get profileUploadIdSuccess;

  /// No description provided for @profileUploadIdError.
  ///
  /// In id, this message translates to:
  /// **'Gagal mengunggah dokumen identitas'**
  String get profileUploadIdError;

  /// No description provided for @profileUploadIdErrorPick.
  ///
  /// In id, this message translates to:
  /// **'Gagal memilih gambar'**
  String get profileUploadIdErrorPick;

  /// No description provided for @profileUploadIdNoImage.
  ///
  /// In id, this message translates to:
  /// **'Silakan pilih gambar terlebih dahulu'**
  String get profileUploadIdNoImage;

  /// No description provided for @profileUploadIdNotLoggedIn.
  ///
  /// In id, this message translates to:
  /// **'Anda harus login untuk mengunggah'**
  String get profileUploadIdNotLoggedIn;

  /// No description provided for @profileUploadIdGuidelines.
  ///
  /// In id, this message translates to:
  /// **'Panduan Unggah'**
  String get profileUploadIdGuidelines;

  /// No description provided for @profileUploadIdGuideline1.
  ///
  /// In id, this message translates to:
  /// **'Pastikan dokumen jelas dan dapat dibaca'**
  String get profileUploadIdGuideline1;

  /// No description provided for @profileUploadIdGuideline2.
  ///
  /// In id, this message translates to:
  /// **'Hindari pantulan cahaya atau bayangan pada dokumen'**
  String get profileUploadIdGuideline2;

  /// No description provided for @profileUploadIdGuideline3.
  ///
  /// In id, this message translates to:
  /// **'Pastikan semua teks dan foto terlihat jelas'**
  String get profileUploadIdGuideline3;

  /// No description provided for @profileUploadIdGuideline4.
  ///
  /// In id, this message translates to:
  /// **'Ukuran file tidak boleh melebihi 5MB'**
  String get profileUploadIdGuideline4;

  /// No description provided for @profileDeactivateAccountTitle.
  ///
  /// In id, this message translates to:
  /// **'Nonaktifkan Akun'**
  String get profileDeactivateAccountTitle;

  /// No description provided for @profileDeactivateAccountSubText.
  ///
  /// In id, this message translates to:
  /// **'Nonaktifkan akun Anda'**
  String get profileDeactivateAccountSubText;

  /// No description provided for @profileDeactivateAccountConfirm.
  ///
  /// In id, this message translates to:
  /// **'Apakah Anda yakin ingin menonaktifkan akun? Tindakan ini tidak dapat dibatalkan.'**
  String get profileDeactivateAccountConfirm;

  /// No description provided for @profileDeactivateAccountButton.
  ///
  /// In id, this message translates to:
  /// **'Nonaktifkan'**
  String get profileDeactivateAccountButton;

  /// No description provided for @profileDeactivateAccountSuccess.
  ///
  /// In id, this message translates to:
  /// **'Akun berhasil dinonaktifkan'**
  String get profileDeactivateAccountSuccess;

  /// No description provided for @profileDeactivateAccountError.
  ///
  /// In id, this message translates to:
  /// **'Gagal menonaktifkan akun'**
  String get profileDeactivateAccountError;

  /// No description provided for @profileDeactivateAccountUserError.
  ///
  /// In id, this message translates to:
  /// **'Tidak dapat memuat informasi pengguna. Silakan coba lagi.'**
  String get profileDeactivateAccountUserError;

  /// No description provided for @profileDeactivateAccountLoading.
  ///
  /// In id, this message translates to:
  /// **'Menonaktifkan akun...'**
  String get profileDeactivateAccountLoading;

  /// No description provided for @bottomBarStartingFrom.
  ///
  /// In id, this message translates to:
  /// **'Mulai dari'**
  String get bottomBarStartingFrom;

  /// No description provided for @bottomBarPerMonth.
  ///
  /// In id, this message translates to:
  /// **'Bulan'**
  String get bottomBarPerMonth;

  /// No description provided for @bottomBarPerDay.
  ///
  /// In id, this message translates to:
  /// **'Hari'**
  String get bottomBarPerDay;

  /// No description provided for @bottomBarContactUs.
  ///
  /// In id, this message translates to:
  /// **'Hubungi Kami'**
  String get bottomBarContactUs;

  /// No description provided for @bottomBarRoomUnavailable.
  ///
  /// In id, this message translates to:
  /// **'Ruangan Tidak Tersedia'**
  String get bottomBarRoomUnavailable;

  /// No description provided for @bottomBarContactCustomerService.
  ///
  /// In id, this message translates to:
  /// **'Hubungi customer service untuk lebih lanjut'**
  String get bottomBarContactCustomerService;

  /// No description provided for @bottomBarSubtotal.
  ///
  /// In id, this message translates to:
  /// **'Subtotal'**
  String get bottomBarSubtotal;

  /// No description provided for @imageViewerClose.
  ///
  /// In id, this message translates to:
  /// **'Tutup'**
  String get imageViewerClose;

  /// No description provided for @imageViewerImageCounter.
  ///
  /// In id, this message translates to:
  /// **'Gambar {current} dari {total}'**
  String imageViewerImageCounter(int current, int total);

  /// No description provided for @detailPropertyPageTitle.
  ///
  /// In id, this message translates to:
  /// **'Detail Properti'**
  String get detailPropertyPageTitle;

  /// No description provided for @roomDetailsPageTitle.
  ///
  /// In id, this message translates to:
  /// **'Detail Kamar'**
  String get roomDetailsPageTitle;

  /// No description provided for @googleSignInNewAccountTitle.
  ///
  /// In id, this message translates to:
  /// **'Akun Baru Dibuat'**
  String get googleSignInNewAccountTitle;

  /// No description provided for @googleSignInNewAccountMessage.
  ///
  /// In id, this message translates to:
  /// **'Akun Anda belum terdaftar sebelumnya.\n\nRegistrasi berhasil. Silakan periksa email Anda untuk memverifikasi akun.'**
  String get googleSignInNewAccountMessage;

  /// No description provided for @googleSignInWelcomeBackTitle.
  ///
  /// In id, this message translates to:
  /// **'Selamat Datang Kembali'**
  String get googleSignInWelcomeBackTitle;

  /// No description provided for @googleSignInWelcomeBackMessage.
  ///
  /// In id, this message translates to:
  /// **'Anda berhasil masuk dengan Google!'**
  String get googleSignInWelcomeBackMessage;

  /// No description provided for @pingSlowConnectionTitle.
  ///
  /// In id, this message translates to:
  /// **'Koneksi Lambat'**
  String get pingSlowConnectionTitle;

  /// No description provided for @pingSlowConnectionMessage.
  ///
  /// In id, this message translates to:
  /// **'Koneksi internet Anda lambat. Ini mungkin mempengaruhi pengalaman Anda.'**
  String get pingSlowConnectionMessage;

  /// No description provided for @pingNoConnectionTitle.
  ///
  /// In id, this message translates to:
  /// **'Tidak Ada Koneksi Internet'**
  String get pingNoConnectionTitle;

  /// No description provided for @pingNoConnectionMessage.
  ///
  /// In id, this message translates to:
  /// **'Tidak dapat terhubung ke internet. Mohon periksa koneksi Anda.'**
  String get pingNoConnectionMessage;

  /// No description provided for @pingDialogOkButton.
  ///
  /// In id, this message translates to:
  /// **'OK'**
  String get pingDialogOkButton;

  /// No description provided for @chatRoomTitle.
  ///
  /// In id, this message translates to:
  /// **'Obrolan'**
  String get chatRoomTitle;

  /// No description provided for @chatInputHint.
  ///
  /// In id, this message translates to:
  /// **'Ketik pesan...'**
  String get chatInputHint;

  /// No description provided for @chatSendButton.
  ///
  /// In id, this message translates to:
  /// **'Kirim'**
  String get chatSendButton;

  /// No description provided for @chatSelectImage.
  ///
  /// In id, this message translates to:
  /// **'Pilih Gambar'**
  String get chatSelectImage;

  /// No description provided for @chatImagePreview.
  ///
  /// In id, this message translates to:
  /// **'Pratinjau Gambar'**
  String get chatImagePreview;

  /// No description provided for @chatMessageEdited.
  ///
  /// In id, this message translates to:
  /// **'Diedit'**
  String get chatMessageEdited;

  /// No description provided for @chatNoMessages.
  ///
  /// In id, this message translates to:
  /// **'Belum ada pesan'**
  String get chatNoMessages;

  /// No description provided for @chatStartConversation.
  ///
  /// In id, this message translates to:
  /// **'Mulai percakapan'**
  String get chatStartConversation;

  /// No description provided for @chatLoadingMessages.
  ///
  /// In id, this message translates to:
  /// **'Memuat pesan...'**
  String get chatLoadingMessages;

  /// No description provided for @chatErrorLoadingMessages.
  ///
  /// In id, this message translates to:
  /// **'Gagal memuat pesan'**
  String get chatErrorLoadingMessages;

  /// No description provided for @chatCreatingConversation.
  ///
  /// In id, this message translates to:
  /// **'Membuat percakapan...'**
  String get chatCreatingConversation;

  /// No description provided for @chatConversationCreated.
  ///
  /// In id, this message translates to:
  /// **'Percakapan berhasil dibuat'**
  String get chatConversationCreated;

  /// No description provided for @chatMessageSent.
  ///
  /// In id, this message translates to:
  /// **'Pesan terkirim'**
  String get chatMessageSent;

  /// No description provided for @chatMessageFailed.
  ///
  /// In id, this message translates to:
  /// **'Gagal mengirim pesan'**
  String get chatMessageFailed;

  /// No description provided for @chatImageTooLarge.
  ///
  /// In id, this message translates to:
  /// **'Ukuran gambar harus kurang dari 10MB'**
  String get chatImageTooLarge;

  /// No description provided for @chatInvalidBooking.
  ///
  /// In id, this message translates to:
  /// **'Anda harus memiliki pemesanan aktif untuk memulai percakapan'**
  String get chatInvalidBooking;

  /// No description provided for @chatDuplicateConversation.
  ///
  /// In id, this message translates to:
  /// **'Percakapan sudah ada'**
  String get chatDuplicateConversation;

  /// No description provided for @chatWithFrontOffice.
  ///
  /// In id, this message translates to:
  /// **'Obrolan dengan Front Office'**
  String get chatWithFrontOffice;

  /// No description provided for @chatWithHeadOffice.
  ///
  /// In id, this message translates to:
  /// **'Obrolan dengan Head Office'**
  String get chatWithHeadOffice;

  /// No description provided for @chatPickFromGallery.
  ///
  /// In id, this message translates to:
  /// **'Galeri'**
  String get chatPickFromGallery;

  /// No description provided for @chatPickFromCamera.
  ///
  /// In id, this message translates to:
  /// **'Kamera'**
  String get chatPickFromCamera;

  /// No description provided for @chatCancel.
  ///
  /// In id, this message translates to:
  /// **'Batal'**
  String get chatCancel;

  /// No description provided for @chatSending.
  ///
  /// In id, this message translates to:
  /// **'Mengirim...'**
  String get chatSending;

  /// No description provided for @chatRetry.
  ///
  /// In id, this message translates to:
  /// **'Coba Lagi'**
  String get chatRetry;

  /// No description provided for @chatLoadMore.
  ///
  /// In id, this message translates to:
  /// **'Muat lebih banyak pesan'**
  String get chatLoadMore;

  /// No description provided for @chatMarkAsRead.
  ///
  /// In id, this message translates to:
  /// **'Tandai sudah dibaca'**
  String get chatMarkAsRead;

  /// No description provided for @chatEditMessage.
  ///
  /// In id, this message translates to:
  /// **'Edit pesan'**
  String get chatEditMessage;

  /// No description provided for @chatDeleteMessage.
  ///
  /// In id, this message translates to:
  /// **'Hapus pesan'**
  String get chatDeleteMessage;

  /// No description provided for @chatCopyMessage.
  ///
  /// In id, this message translates to:
  /// **'Salin pesan'**
  String get chatCopyMessage;

  /// No description provided for @chatImageUploadError.
  ///
  /// In id, this message translates to:
  /// **'Gagal mengunggah gambar'**
  String get chatImageUploadError;

  /// No description provided for @chatNetworkError.
  ///
  /// In id, this message translates to:
  /// **'Kesalahan jaringan. Mohon periksa koneksi Anda.'**
  String get chatNetworkError;

  /// No description provided for @chatServerError.
  ///
  /// In id, this message translates to:
  /// **'Kesalahan server. Silakan coba lagi nanti.'**
  String get chatServerError;

  /// No description provided for @chatUnknownError.
  ///
  /// In id, this message translates to:
  /// **'Terjadi kesalahan yang tidak diketahui'**
  String get chatUnknownError;

  /// No description provided for @chatImageFormatError.
  ///
  /// In id, this message translates to:
  /// **'Hanya gambar JPG dan PNG yang diperbolehkan'**
  String get chatImageFormatError;

  /// No description provided for @chatImagePickError.
  ///
  /// In id, this message translates to:
  /// **'Gagal memilih gambar. Silakan coba lagi.'**
  String get chatImagePickError;

  /// No description provided for @showAll.
  ///
  /// In id, this message translates to:
  /// **'Lihat Semua'**
  String get showAll;

  /// No description provided for @loadingPropertyName.
  ///
  /// In id, this message translates to:
  /// **'Memuat Nama Properti'**
  String get loadingPropertyName;

  /// No description provided for @loadingAddress.
  ///
  /// In id, this message translates to:
  /// **'Memuat Alamat'**
  String get loadingAddress;

  /// No description provided for @loadingDistance.
  ///
  /// In id, this message translates to:
  /// **'Memuat Jarak'**
  String get loadingDistance;

  /// No description provided for @loadingType.
  ///
  /// In id, this message translates to:
  /// **'Memuat'**
  String get loadingType;

  /// No description provided for @loadingBestSellerProperty.
  ///
  /// In id, this message translates to:
  /// **'Memuat Properti Best Seller'**
  String get loadingBestSellerProperty;

  /// No description provided for @loadingBudgetProperty.
  ///
  /// In id, this message translates to:
  /// **'Memuat Properti Budget'**
  String get loadingBudgetProperty;

  /// No description provided for @loadingRoomName.
  ///
  /// In id, this message translates to:
  /// **'Memuat Nama Kamar'**
  String get loadingRoomName;

  /// No description provided for @loadingDescription.
  ///
  /// In id, this message translates to:
  /// **'Memuat Deskripsi'**
  String get loadingDescription;

  /// No description provided for @loadingStatus.
  ///
  /// In id, this message translates to:
  /// **'Memuat Status'**
  String get loadingStatus;

  /// No description provided for @loadingRoomType.
  ///
  /// In id, this message translates to:
  /// **'Tipe Kamar: Memuat'**
  String get loadingRoomType;

  /// No description provided for @loadingPrice.
  ///
  /// In id, this message translates to:
  /// **'Harga: Rp 0 / malam'**
  String get loadingPrice;

  /// No description provided for @loadingPropertyType.
  ///
  /// In id, this message translates to:
  /// **'Memuat Tipe Properti'**
  String get loadingPropertyType;

  /// No description provided for @loading.
  ///
  /// In id, this message translates to:
  /// **'Memuat'**
  String get loading;

  /// No description provided for @helpCenter.
  ///
  /// In id, this message translates to:
  /// **'Pusat Bantuan'**
  String get helpCenter;

  /// No description provided for @faqTabLabel.
  ///
  /// In id, this message translates to:
  /// **'FAQ'**
  String get faqTabLabel;

  /// No description provided for @contactUsTabLabel.
  ///
  /// In id, this message translates to:
  /// **'Hubungi Kami'**
  String get contactUsTabLabel;

  /// No description provided for @noActiveBookings.
  ///
  /// In id, this message translates to:
  /// **'Belum Ada Pesanan'**
  String get noActiveBookings;

  /// No description provided for @noActiveBookingsMessage.
  ///
  /// In id, this message translates to:
  /// **'Anda belum memiliki pesanan. Mulai jelajahi properti kami untuk menemukan tempat yang sempurna!'**
  String get noActiveBookingsMessage;

  /// No description provided for @browseProperties.
  ///
  /// In id, this message translates to:
  /// **'Jelajahi Properti'**
  String get browseProperties;

  /// No description provided for @selectAppleAccount.
  ///
  /// In id, this message translates to:
  /// **'Pilih Akun Apple'**
  String get selectAppleAccount;

  /// No description provided for @selectAccountForLogin.
  ///
  /// In id, this message translates to:
  /// **'Pilih akun yang ingin digunakan untuk login:'**
  String get selectAccountForLogin;

  /// No description provided for @verified.
  ///
  /// In id, this message translates to:
  /// **'Terverifikasi'**
  String get verified;

  /// No description provided for @notVerified.
  ///
  /// In id, this message translates to:
  /// **'Belum Verifikasi'**
  String get notVerified;

  /// No description provided for @useAnotherAppleAccount.
  ///
  /// In id, this message translates to:
  /// **'Gunakan akun Apple lain'**
  String get useAnotherAppleAccount;

  /// No description provided for @switchAccount.
  ///
  /// In id, this message translates to:
  /// **'Ganti Akun'**
  String get switchAccount;

  /// No description provided for @addAccount.
  ///
  /// In id, this message translates to:
  /// **'Tambah Akun'**
  String get addAccount;

  /// No description provided for @addAnotherAccount.
  ///
  /// In id, this message translates to:
  /// **'Tambah Akun Lain'**
  String get addAnotherAccount;

  /// No description provided for @removeButton.
  ///
  /// In id, this message translates to:
  /// **'Hapus'**
  String get removeButton;

  /// No description provided for @removeAccountDialogTitle.
  ///
  /// In id, this message translates to:
  /// **'Hapus Akun'**
  String get removeAccountDialogTitle;

  /// No description provided for @removeAccountTooltip.
  ///
  /// In id, this message translates to:
  /// **'Hapus akun'**
  String get removeAccountTooltip;

  /// No description provided for @accountRemovedSuccess.
  ///
  /// In id, this message translates to:
  /// **'Akun berhasil dihapus'**
  String get accountRemovedSuccess;

  /// No description provided for @failedToRemoveAccount.
  ///
  /// In id, this message translates to:
  /// **'Gagal menghapus akun'**
  String get failedToRemoveAccount;

  /// No description provided for @failedToSwitchAccount.
  ///
  /// In id, this message translates to:
  /// **'Gagal mengganti akun'**
  String get failedToSwitchAccount;

  /// No description provided for @noAccountsFound.
  ///
  /// In id, this message translates to:
  /// **'Tidak ada akun ditemukan'**
  String get noAccountsFound;

  /// No description provided for @signInWithAppleToAddAccount.
  ///
  /// In id, this message translates to:
  /// **'Masuk dengan Apple untuk menambah akun'**
  String get signInWithAppleToAddAccount;

  /// No description provided for @activeStatus.
  ///
  /// In id, this message translates to:
  /// **'Aktif'**
  String get activeStatus;

  /// No description provided for @vaGenerationFailed.
  ///
  /// In id, this message translates to:
  /// **'Gagal Membuat VA'**
  String get vaGenerationFailed;

  /// No description provided for @vaGenerationError.
  ///
  /// In id, this message translates to:
  /// **'Booking berhasil disimpan, namun gagal membuat Virtual Account.'**
  String get vaGenerationError;

  /// No description provided for @qrisGenerationFailed.
  ///
  /// In id, this message translates to:
  /// **'Gagal Membuat QRIS'**
  String get qrisGenerationFailed;

  /// No description provided for @qrisGenerationError.
  ///
  /// In id, this message translates to:
  /// **'Booking berhasil disimpan, namun gagal membuat QRIS.'**
  String get qrisGenerationError;

  /// No description provided for @ccPaymentFailed.
  ///
  /// In id, this message translates to:
  /// **'Pembayaran Kartu Kredit Gagal'**
  String get ccPaymentFailed;

  /// No description provided for @ccPaymentError.
  ///
  /// In id, this message translates to:
  /// **'Booking berhasil disimpan, namun gagal memproses pembayaran kartu kredit.'**
  String get ccPaymentError;

  /// No description provided for @errorDetail.
  ///
  /// In id, this message translates to:
  /// **'Detail Error:'**
  String get errorDetail;

  /// No description provided for @retryFromBookingDetail.
  ///
  /// In id, this message translates to:
  /// **'Anda dapat mencoba membuat VA/QRIS lagi dari halaman detail booking.'**
  String get retryFromBookingDetail;

  /// No description provided for @goToMyBooking.
  ///
  /// In id, this message translates to:
  /// **'Ke Pesanan Saya'**
  String get goToMyBooking;

  /// No description provided for @indonesianLanguage.
  ///
  /// In id, this message translates to:
  /// **'Indonesia'**
  String get indonesianLanguage;

  /// No description provided for @englishLanguage.
  ///
  /// In id, this message translates to:
  /// **'English'**
  String get englishLanguage;

  /// No description provided for @registrationDisabled.
  ///
  /// In id, this message translates to:
  /// **'Registrasi Tidak Tersedia'**
  String get registrationDisabled;

  /// No description provided for @registrationDisabledMessage.
  ///
  /// In id, this message translates to:
  /// **'Fitur registrasi sementara dinonaktifkan. Silakan coba lagi nanti.'**
  String get registrationDisabledMessage;

  /// No description provided for @notLoggedIn.
  ///
  /// In id, this message translates to:
  /// **'Belum masuk'**
  String get notLoggedIn;

  /// No description provided for @edited.
  ///
  /// In id, this message translates to:
  /// **'Diedit'**
  String get edited;

  /// No description provided for @greetingMorning.
  ///
  /// In id, this message translates to:
  /// **'Selamat pagi'**
  String get greetingMorning;

  /// No description provided for @greetingAfternoon.
  ///
  /// In id, this message translates to:
  /// **'Selamat siang'**
  String get greetingAfternoon;

  /// No description provided for @greetingEvening.
  ///
  /// In id, this message translates to:
  /// **'Selamat sore'**
  String get greetingEvening;

  /// No description provided for @greetingNight.
  ///
  /// In id, this message translates to:
  /// **'Selamat malam'**
  String get greetingNight;

  /// No description provided for @homeSubtitle.
  ///
  /// In id, this message translates to:
  /// **'Mau tinggal dimana hari ini?'**
  String get homeSubtitle;

  /// No description provided for @homeBannerTitle.
  ///
  /// In id, this message translates to:
  /// **'Temukan Hunian Impianmu'**
  String get homeBannerTitle;

  /// No description provided for @homeBannerPart1.
  ///
  /// In id, this message translates to:
  /// **'Temukan '**
  String get homeBannerPart1;

  /// No description provided for @homeBannerPart2.
  ///
  /// In id, this message translates to:
  /// **'Hunian'**
  String get homeBannerPart2;

  /// No description provided for @homeBannerPart3.
  ///
  /// In id, this message translates to:
  /// **' Impianmu'**
  String get homeBannerPart3;

  /// No description provided for @genderMale.
  ///
  /// In id, this message translates to:
  /// **'Pria'**
  String get genderMale;

  /// No description provided for @genderFemale.
  ///
  /// In id, this message translates to:
  /// **'Wanita'**
  String get genderFemale;

  /// No description provided for @genderMixed.
  ///
  /// In id, this message translates to:
  /// **'Campuran'**
  String get genderMixed;

  /// No description provided for @scanQRWithEwallet.
  ///
  /// In id, this message translates to:
  /// **'Pindai kode QR ini dengan aplikasi e-wallet Anda'**
  String get scanQRWithEwallet;

  /// No description provided for @downloadQR.
  ///
  /// In id, this message translates to:
  /// **'Unduh Kode QR'**
  String get downloadQR;

  /// No description provided for @downloadQRSuccess.
  ///
  /// In id, this message translates to:
  /// **'Kode QR berhasil disimpan ke galeri'**
  String get downloadQRSuccess;

  /// No description provided for @downloadQRFailed.
  ///
  /// In id, this message translates to:
  /// **'Gagal menyimpan kode QR'**
  String get downloadQRFailed;

  /// No description provided for @downloadQRPermissionDenied.
  ///
  /// In id, this message translates to:
  /// **'Izin ditolak untuk menyimpan gambar'**
  String get downloadQRPermissionDenied;

  /// No description provided for @creditCardPayment.
  ///
  /// In id, this message translates to:
  /// **'Pembayaran Kartu Kredit'**
  String get creditCardPayment;

  /// No description provided for @continuePayment.
  ///
  /// In id, this message translates to:
  /// **'Lanjutkan Pembayaran'**
  String get continuePayment;

  /// No description provided for @ccPaymentNote.
  ///
  /// In id, this message translates to:
  /// **'Klik tombol di atas untuk menyelesaikan pembayaran kartu kredit Anda'**
  String get ccPaymentNote;

  /// No description provided for @expiresIn.
  ///
  /// In id, this message translates to:
  /// **'Berakhir dalam'**
  String get expiresIn;

  /// No description provided for @darkModeLabel.
  ///
  /// In id, this message translates to:
  /// **'Mode Gelap'**
  String get darkModeLabel;

  /// No description provided for @lightModeLabel.
  ///
  /// In id, this message translates to:
  /// **'Mode Terang'**
  String get lightModeLabel;

  /// No description provided for @switchToLightMode.
  ///
  /// In id, this message translates to:
  /// **'Beralih ke mode terang'**
  String get switchToLightMode;

  /// No description provided for @switchToDarkMode.
  ///
  /// In id, this message translates to:
  /// **'Beralih ke mode gelap'**
  String get switchToDarkMode;
}

class _AppLocalizationsDelegate
    extends LocalizationsDelegate<AppLocalizations> {
  const _AppLocalizationsDelegate();

  @override
  Future<AppLocalizations> load(Locale locale) {
    return SynchronousFuture<AppLocalizations>(lookupAppLocalizations(locale));
  }

  @override
  bool isSupported(Locale locale) =>
      <String>['en', 'id', 'zh'].contains(locale.languageCode);

  @override
  bool shouldReload(_AppLocalizationsDelegate old) => false;
}

AppLocalizations lookupAppLocalizations(Locale locale) {
  // Lookup logic when only language code is specified.
  switch (locale.languageCode) {
    case 'en':
      return AppLocalizationsEn();
    case 'id':
      return AppLocalizationsId();
    case 'zh':
      return AppLocalizationsZh();
  }

  throw FlutterError(
    'AppLocalizations.delegate failed to load unsupported locale "$locale". This is likely '
    'an issue with the localizations generation tool. Please file an issue '
    'on GitHub with a reproducible sample app and the gen-l10n configuration '
    'that was used.',
  );
}
