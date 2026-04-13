// ignore: unused_import
import 'package:intl/intl.dart' as intl;
import 'app_localizations.dart';

// ignore_for_file: type=lint

/// The translations for English (`en`).
class AppLocalizationsEn extends AppLocalizations {
  AppLocalizationsEn([String locale = 'en']) : super(locale);

  @override
  String get searchBannerTitle => 'Search';

  @override
  String get browseAll => 'All Properties';

  @override
  String get categories => 'Categories';

  @override
  String get availableNow => 'Available Now';

  @override
  String get filterCategoryAll => 'All';

  @override
  String get popularArea => 'Popular Area';

  @override
  String get budget => 'Near You';

  @override
  String get promotion => 'Promotion';

  @override
  String get promoBanner => 'Special Promo';

  @override
  String get promoDetailTitle => 'Promo Details';

  @override
  String get promoDescription => 'Promo Description';

  @override
  String get promoCodeLabel => 'Promo Code';

  @override
  String get noDescription => 'No description';

  @override
  String get promoHowToClaim => 'How to Claim';

  @override
  String get promoClaimStep1 => 'Choose the desired property';

  @override
  String get promoClaimStep2 => 'Then select the desired room';

  @override
  String get promoClaimStep3 => 'Enter Voucher Code when booking';

  @override
  String get promoClaimStep4 => 'Complete the booking';

  @override
  String get promoClaimStep5 => 'Enjoy your promo/discount';

  @override
  String get promoTermsTitle => 'Terms & Conditions';

  @override
  String get promoTerm1 => 'Terms and conditions apply';

  @override
  String get promoTerm2 => 'Cannot be combined with other promos';

  @override
  String get promoTerm3 => 'Promo period is limited';

  @override
  String get promoTerm4 => 'Only valid for limited users';

  @override
  String get promoLoadError => 'Failed to load promo details';

  @override
  String get homeLabel => 'Home';

  @override
  String get myBookingLabel => 'My Booking';

  @override
  String get umLabel => 'UM';

  @override
  String get csLabel => 'CS';

  @override
  String get profileLabel => 'Profile';

  @override
  String get popularAreaJakarta => 'Jakarta';

  @override
  String get detailJakarta => 'Metropolitan business and entertainment hub';

  @override
  String get popularAreaBogor => 'Bogor';

  @override
  String get detailBogor =>
      'The city of rain with a cool and natural atmosphere';

  @override
  String get welcomeTagline => 'Find Your Perfect Stay';

  @override
  String get welcomeMessage => 'Your journey to comfort begins here';

  @override
  String get welcomeGetStartedButton => 'Get Started';

  @override
  String get welcomeSignUpPrompt => 'Doesn\'t have account? ';

  @override
  String get welcomeSignUpButton => 'Sign Up';

  @override
  String get filterCategoryKos => 'House';

  @override
  String get filterCategoryApartment => 'Apartment';

  @override
  String get filterCategoryHotel => 'Hotel';

  @override
  String get filterCategoryVilla => 'Villa';

  @override
  String get filterLabelCategory => 'Category';

  @override
  String get filterLabelRentType => 'Rent Type';

  @override
  String get filterRentTypeDaily => 'Daily';

  @override
  String get filterRentTypeMonthly => 'Monthly';

  @override
  String get filterLabelCheckIn => 'Check-in Date';

  @override
  String get filterHintCheckIn => 'Select a date';

  @override
  String get filterLabelDuration => 'Duration';

  @override
  String get filterHintDurationDays => 'Enter number of days';

  @override
  String get filterHintDurationMonths => 'Enter number of months';

  @override
  String get filterSuffixDays => 'Day';

  @override
  String get filterSuffixMonths => 'Month';

  @override
  String get filterLabelCheckOut => 'Check-out Date';

  @override
  String get filterHintCheckOut => 'Auto-filled';

  @override
  String get filterButtonSearch => 'Search';

  @override
  String get loginButton => 'Login';

  @override
  String get registerButton => 'Register Now';

  @override
  String get forgotPasswordTitle => 'Forgot Password';

  @override
  String get forgotPasswordSubtitle =>
      'Don\'t worry, just enter your email and create a new password';

  @override
  String get emailInputHint => 'Enter Email';

  @override
  String get sendCodeButton => 'Send Code';

  @override
  String get rememberPasswordPrompt => 'Remember Password?';

  @override
  String get loginButtonText => 'Login';

  @override
  String get emailEmptyError => 'Email cannot be empty';

  @override
  String get emailInvalidError => 'Invalid email format';

  @override
  String get passwordResetSuccess =>
      'A password reset link has been sent to your email!';

  @override
  String get passwordResetError =>
      'An error occurred while sending the request.';

  @override
  String get availableStatus => 'Available';

  @override
  String get unavailableStatus => 'Unavailable';

  @override
  String get unknownStatus => 'Unknown';

  @override
  String startingFrom(Object price) {
    return 'Starting from $price/Month';
  }

  @override
  String get loginWelcomeTitle => 'Hello! Welcome back';

  @override
  String get loginEmailHint => 'Enter your email';

  @override
  String get loginPasswordHint => 'Enter your password';

  @override
  String get loginEmptyError => 'Some required fields cannot be empty';

  @override
  String get loginInvalidFormatError =>
      'The login address you entered is not valid. Please use a valid email or phone number format.';

  @override
  String get loginPasswordLengthError =>
      'Your password must be at least 8 characters long.';

  @override
  String get loginIncorrectCredentials =>
      'Email/phone number and password must match.';

  @override
  String get loginUnknownError => 'An error occurred during login.';

  @override
  String get loginUnknownNetworkError => 'An unknown network error occurred.';

  @override
  String loginGeneralError(Object error) {
    return 'An error occurred: $error';
  }

  @override
  String get loginOrText => 'Or';

  @override
  String get loginNoAccountPrompt => 'Don\'t have an account?';

  @override
  String get loginAsGuestButton => 'Continue as Guest?';

  @override
  String get rememberMe => 'Remember Me';

  @override
  String get biometricAuthFailed =>
      'Biometric authentication failed. Please try again.';

  @override
  String get biometricAuthNotConfigured =>
      'Biometric authentication is not yet configured. Please log in manually first and fill the remember me.';

  @override
  String get emailNotVerifiedTitle => 'Email Not Verified';

  @override
  String get emailNotVerifiedMessage =>
      'Please verify your email first to continue. Check your email inbox and click the verification link we sent you.';

  @override
  String get emailVerifyResend => 'Resend Email';

  @override
  String get emailVerifyChangeEmail => 'Change Email';

  @override
  String get emailVerifyLater => 'Later';

  @override
  String get emailVerifySentTitle => 'Email Sent!';

  @override
  String get emailVerifySentMessage =>
      'Verification email has been sent. Please check your inbox or spam folder.';

  @override
  String get emailVerifyChangeTitle => 'Change Email Address';

  @override
  String get emailVerifyNewEmailHint => 'Enter new email';

  @override
  String get emailVerifyChangeConfirm => 'Change & Send Verification';

  @override
  String get accountDeactivatedTitle => 'Account Deactivated';

  @override
  String get accountDeactivatedMessage =>
      'Your account has been deactivated. Please contact support for more information.';

  @override
  String get registerWelcomeTitle => 'Hello! Register to get started';

  @override
  String get registerFormError =>
      'Please fill in all required fields correctly.';

  @override
  String get registerSuccessMessage =>
      'Register Success, please verify your email and relogin';

  @override
  String get registerUnknownError =>
      'An unknown error occurred during registration.';

  @override
  String get registersuccessnotif =>
      'Register Success, please verify your email and relogin';

  @override
  String get firstNameLabel => 'First Name';

  @override
  String get firstNameEmptyError => 'First name cannot be empty';

  @override
  String get lastNameLabel => 'Last Name';

  @override
  String get lastNameEmptyError => 'Last name cannot be empty';

  @override
  String get registerOneNameLabel => 'I only have first name';

  @override
  String get fullNameLabel => 'Full Name';

  @override
  String get nameEmptyError => 'Full Name is required';

  @override
  String get usernameLabel => 'Username';

  @override
  String get usernameEmptyError => 'Username cannot be empty';

  @override
  String get emailLabel => 'Email';

  @override
  String get phoneNumberLabel => 'Phone Number';

  @override
  String get phoneNumberHint => 'Enter Phone Number';

  @override
  String get phoneNumberEmptyError => 'Phone number cannot be empty';

  @override
  String get passwordLabel => 'Password';

  @override
  String get passwordEmptyError => 'Password cannot be empty';

  @override
  String get passwordLengthError =>
      'Password must be at least 8 characters long.';

  @override
  String get passwordLengthInfo =>
      'Password must be at least 8 characters long.';

  @override
  String get confirmPasswordLabel => 'Confirm password';

  @override
  String get confirmPasswordEmptyError => 'Confirm password cannot be empty';

  @override
  String get passwordMismatchError =>
      'Password and confirm password do not match.';

  @override
  String get confirmPasswordInfo =>
      'Re-enter the same password for confirmation.';

  @override
  String get registerOrText => 'Or register with';

  @override
  String get registerHaveAccountPrompt => 'Already have an account?';

  @override
  String get updatePasswordTitle => 'Update Your Password';

  @override
  String get updatePasswordSuccessMessage => 'Password updated successfully!';

  @override
  String get updatePasswordFormError => 'Please fill in all fields correctly.';

  @override
  String get updatePasswordUserIdError =>
      'Error: User ID not found. Please log in again.';

  @override
  String get oldPasswordLabel => 'Old Password';

  @override
  String get oldPasswordEmptyError => 'Old password cannot be empty';

  @override
  String get newPasswordLabel => 'New Password';

  @override
  String get newPasswordEmptyError => 'New password cannot be empty';

  @override
  String get newPasswordLengthError =>
      'Password must be at least 8 characters long.';

  @override
  String get newPasswordLengthInfo =>
      'New password must be at least 8 characters long.';

  @override
  String get confirmNewPasswordLabel => 'Confirm New Password';

  @override
  String get confirmNewPasswordEmptyError =>
      'Confirm new password cannot be empty';

  @override
  String get newPasswordMismatchError =>
      'Password confirmation does not match.';

  @override
  String get confirmNewPasswordInfo =>
      'Re-enter the same new password for confirmation.';

  @override
  String get updatePasswordButton => 'Update Password';

  @override
  String get profileTitle => 'Profile';

  @override
  String get profileDefaultUsername => 'User';

  @override
  String get profileDefaultEmail => 'user@example.com';

  @override
  String get profileWelcomeText => 'Welcome';

  @override
  String get profileUserProfileMenu => 'User Profile';

  @override
  String get changePasswordTitle => 'Change Password';

  @override
  String get changePasswordSubText => 'Update your account password';

  @override
  String get profileContactUsMenu => 'Contact Us';

  @override
  String get profileDeactivateAccountMenu => 'Deactivate Account';

  @override
  String get logoutTitle => 'Logout';

  @override
  String get logoutSubText => 'Log out from the account';

  @override
  String get confirmLogoutMessage =>
      'Are you sure you want to log out of this account?';

  @override
  String get cancelButton => 'Cancel';

  @override
  String get logoutButton => 'Logout';

  @override
  String get logoutErrorMessage => 'Failed to log out';

  @override
  String get additionalSectionTitle => 'Additional';

  @override
  String get helpCenterTitle => 'Help Center';

  @override
  String get aboutTitle => 'About';

  @override
  String get deactivateAccountTitle => 'Delete Account';

  @override
  String get deactivateAccountSubText => 'Disable your account';

  @override
  String get confirmDeactivateAccountMessage =>
      'Are you sure you want to deactivate your account? This action cannot be undone.';

  @override
  String get deactivateButton => 'Deactivate';

  @override
  String get deactivateSuccessMessage => 'Account deactivated successfully.';

  @override
  String get deactivateErrorMessage => 'Failed to deactivate account.';

  @override
  String get deactivateUserIdNotFoundError => 'User ID not found';

  @override
  String get deactivateAccountFailedError => 'Failed to deactivate account';

  @override
  String get deactivateAccountUnknownError => 'Unknown error';

  @override
  String get updateProfileTitle => 'Update Profile';

  @override
  String get updateProfileSuccessMessage => 'Profile Updated Successfully';

  @override
  String get profileImageLoadError => 'Failed to load profile picture';

  @override
  String get uploadProfilePhotoPrompt => 'Tap to upload profile photo';

  @override
  String get pickImageSourceTitle => 'Select Image Source';

  @override
  String get cameraOption => 'Camera';

  @override
  String get galleryOption => 'Gallery';

  @override
  String get firstNameHint => 'Your First Name';

  @override
  String get lastNameHint => 'Your Last Name';

  @override
  String get usernameHint => 'Your Username';

  @override
  String get emailHint => 'Your Email';

  @override
  String get updateProfileButton => 'Update Profile';

  @override
  String get myBookingTitle => 'My Booking';

  @override
  String get upcomingTab => 'Upcoming';

  @override
  String get completedTab => 'Completed';

  @override
  String get errorLoadingData => 'Error loading data';

  @override
  String get myBookingEmptyTitle => 'No Bookings Yet';

  @override
  String get myBookingEmptyMessage =>
      'You haven\'t made any bookings. Start exploring our properties to find your perfect place!';

  @override
  String get myBookingBrowseProperties => 'Browse Properties';

  @override
  String get csTitle => 'Customer Service';

  @override
  String get csNoBookings => 'No Active Bookings';

  @override
  String get csNoBookingsDesc =>
      'You don\'t have any active bookings to chat about. Book a property first.';

  @override
  String get csSelectRecipient => 'Select Recipient';

  @override
  String get csFrontOffice => 'Front Office';

  @override
  String get csHeadOffice => 'Head Office Finance';

  @override
  String get csHeadOfficeDesc => 'Finance and payment inquiries';

  @override
  String get backToHome => 'Back to Home';

  @override
  String get tryAgain => 'Try Again';

  @override
  String get cancel => 'Cancel';

  @override
  String get checkInLabel => 'Check-In';

  @override
  String get checkOutLabel => 'Check-Out';

  @override
  String get myBookingDetailSelectImageFirst => 'ℹ️ Select an image first';

  @override
  String get myBookingDetailUploadSuccess => '✅ Image uploaded successfully';

  @override
  String get myBookingDetailUploadFailed => '❌ Failed to upload image';

  @override
  String get myBookingDetailDataNotFound => 'Booking data not found.';

  @override
  String get myBookingDetailPropertyNameDefault => 'Property Name';

  @override
  String get myBookingDetailRoomNameDefault => 'Room Name';

  @override
  String get myBookingDetailPaymentProofWarningTitle =>
      'Payment proof has not been uploaded';

  @override
  String get myBookingDetailPaymentProofWarningMessage =>
      'Please upload your payment proof to avoid booking cancellation.';

  @override
  String get myBookingDetailTitle => 'Booking Details';

  @override
  String get myBookingDetailOrderId => 'Order ID:';

  @override
  String get myBookingDetailPhoneNumber => 'Phone Number:';

  @override
  String get myBookingDetailBookingType => 'Booking Type:';

  @override
  String get myBookingDetailDuration => 'Duration:';

  @override
  String get myBookingDetailTimeTitle => 'Time Details';

  @override
  String get myBookingDetailCheckIn => 'Check-In Date:';

  @override
  String get myBookingDetailCheckOut => 'Check-Out Date:';

  @override
  String get myBookingDetailBookingPriceTitle => 'Booking Price';

  @override
  String get myBookingDetailPricePerDay => 'Price per day:';

  @override
  String get myBookingDetailPricePerMonth => 'Price per month:';

  @override
  String get myBookingDetailSubtotal => 'Subtotal:';

  @override
  String get myBookingDetailSubtotalBeforeDiscount =>
      'Subtotal Before Discount:';

  @override
  String get myBookingDetailVoucher => 'Voucher';

  @override
  String get myBookingDetailDeposit => 'Deposit:';

  @override
  String get myBookingDetailNumberOfDays => 'Number of days:';

  @override
  String get myBookingDetailNumberOfMonths => 'Number of months:';

  @override
  String get myBookingDetailTotalPriceTitle => 'Total Price';

  @override
  String get myBookingDetailServiceFee => 'Service Fee:';

  @override
  String get myBookingDetailGrandtotal => 'Total Payment:';

  @override
  String get myBookingDetailPaymentProofTitle => 'Payment Proof';

  @override
  String get myBookingDetailPickFromGallery => 'Pick from Gallery';

  @override
  String get myBookingDetailTakePhoto => 'Take Photo';

  @override
  String get myBookingDetailUploading => 'Uploading payment proof...';

  @override
  String get myBookingDetailUploadPaymentProof => 'Upload Payment Proof';

  @override
  String get myBookingDetailUploadThisImage => 'Upload This Image';

  @override
  String get myBookingDetailError => 'Error';

  @override
  String get myBookingDetailAppBarTitle => 'My Booking Details';

  @override
  String get cameraNotFoundError => 'No camera found.';

  @override
  String get cameraInitFailed => 'Failed to initialize camera';

  @override
  String get cameraUnexpectedError => 'An unexpected error occurred';

  @override
  String get cameraSelectFirstError => 'Error: Select a camera first.';

  @override
  String get cameraCaptureFailed => 'Failed to capture image';

  @override
  String get cameraPopupTitle => 'Upload Payment Proof';

  @override
  String get cameraGalleryTooltip => 'Choose from Gallery';

  @override
  String get cameraTryAgain => 'Try Again';

  @override
  String get cameraGalleryButton => 'Choose from Gallery';

  @override
  String get cameraRetake => 'Retake';

  @override
  String get cameraUseThisImage => 'Use This Image';

  @override
  String get viewerHideAttachment => 'Hide Attachment';

  @override
  String get viewerShowPaymentProof => 'View Payment Proof';

  @override
  String get viewerUpdatePaymentProof => 'Update Payment Proof';

  @override
  String get propertyTypePageTitle => 'Explore Property Types';

  @override
  String get propertyTypeNoActiveFound => 'No active property types found.';

  @override
  String get propertyTypeFailedToLoad => 'Failed to load property types';

  @override
  String get searchResultTitle => 'Search Results';

  @override
  String get searchResultFailedToLoad => 'Failed to load results';

  @override
  String get detailPropertyMonth => 'Month';

  @override
  String get detailPropertyError => 'Error';

  @override
  String get detailPropertyNameNotAvailable => 'Name Not Available';

  @override
  String get detailPropertyTagNotAvailable => 'Tag Not Available';

  @override
  String detailPropertyFloorCount(num count) {
    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '$count Floors',
      one: '1 Floor',
    );
    return '$_temp0';
  }

  @override
  String get detailPropertyFacilitiesTitle => 'Property Facilities';

  @override
  String get detailPropertyNoFacilities => 'No facilities available.';

  @override
  String get roomTypeAvailableRooms => 'Available Rooms';

  @override
  String get roomTypeNoRoomsAvailable => 'No rooms available';

  @override
  String get contactBarPriceLabel => 'Price Starting From';

  @override
  String get contactBarSafetyLabel => 'Promo';

  @override
  String get contactBarLoginRequired =>
      'Please log in first to proceed with payment.';

  @override
  String get contactBarLoginButton => 'Login';

  @override
  String get contactBarProfileRequired =>
      'Please fill out your personal information first to proceed with payment.';

  @override
  String get contactBarProfileButton => 'Profile';

  @override
  String get contactBarPhoneRequired =>
      'Please add your phone number first to proceed with booking.';

  @override
  String get contactBarPhoneButton => 'Add Number';

  @override
  String get contactBarIncompleteOrder => 'Please complete all booking data.';

  @override
  String get contactBarBookNowButton => 'Book Now';

  @override
  String get contactBarOtherPropertiesButton => 'Other Properties';

  @override
  String get roomDetailsError => 'Error';

  @override
  String get roomDetailsLoading => 'Loading room details...';

  @override
  String get roomDetailsDaily => 'Day';

  @override
  String get roomDetailsMonthly => 'Month';

  @override
  String get roomDetailsCompleteForm => 'Please complete all booking data.';

  @override
  String get roomDetailsPerMonth => '/Month';

  @override
  String get roomDetailsPerDay => '/Day';

  @override
  String get roomDetailsFloor => 'Floor ';

  @override
  String get roomDetailsArea => 'Area ';

  @override
  String get roomDetailsCapacity => 'Capacity ';

  @override
  String get roomBookinginfoTitle => 'Booking Information';

  @override
  String roomDetailsCapacityCount(num count) {
    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '$count People',
      one: '1 Person',
    );
    return '$_temp0';
  }

  @override
  String get roomDetailsBed => 'Bed ';

  @override
  String get roomDetailsFacilitiesTitle => 'Room Facilities';

  @override
  String get roomDetailsDailyPricing => 'Daily Pricing';

  @override
  String get roomDetailsDailyPricingLoading =>
      'Calculating per-date pricing...';

  @override
  String get roomDetailsNoFacilities => 'No facilities available';

  @override
  String get roomDetailsCheckingAvailability => 'Checking availability...';

  @override
  String get roomDetailsFailedToCheckAvailability =>
      'Failed to check availability.';

  @override
  String get roomDetailsRoomAvailable => 'Room available';

  @override
  String get roomDetailsRoomNotAvailable => 'Room not available on those dates';

  @override
  String get roomDetailsRoomStatusNotAvailable => 'Room Not Available';

  @override
  String get roomDetailsRoomStatusUnderMaintenance => 'Room Under Maintenance';

  @override
  String get roomDetailsRoomStatusCurrentlyRented => 'Room Currently Rented';

  @override
  String get roomDetailsRoomStatusOccupied => 'Room Occupied';

  @override
  String get roomDetailsRoomStatusCannotBook => 'Room Cannot Be Booked';

  @override
  String get roomDetailsLoginRequired => 'You must be logged in to book';

  @override
  String get roomDetailsProfilePictureRequired =>
      'Please complete your ID Card to book';

  @override
  String get roomDetailsAdditionalFeesTitle => 'Additional Fees';

  @override
  String get roomDetailsDepositFee => 'Deposit';

  @override
  String get roomDetailsDepositNote => 'Deposit will be refunded at checkout';

  @override
  String get roomDetailsParkingCar => 'Car Parking';

  @override
  String get roomDetailsParkingMotorcycle => 'Motorcycle Parking';

  @override
  String get roomDetailsParkingOptional => 'Optional';

  @override
  String get renewBookingTitle => 'Renew Booking';

  @override
  String get renewBookingButton => 'Renew Booking';

  @override
  String get renewBookingPeriodLabel => 'Period';

  @override
  String get renewBookingPeriodDaily => 'Daily';

  @override
  String get renewBookingPeriodMonthly => 'Monthly';

  @override
  String get renewBookingDurationLabel => 'Duration';

  @override
  String get renewBookingDurationDay => 'Day(s)';

  @override
  String get renewBookingDurationMonth => 'Month(s)';

  @override
  String get renewBookingCheckInLabel => 'Check-in Date';

  @override
  String get renewBookingCheckOutLabel => 'Check-out Date';

  @override
  String get renewBookingRoomAvailable => 'Room available';

  @override
  String get renewBookingRoomNotAvailable =>
      'Room not available for these dates';

  @override
  String get renewBookingCheckingAvailability => 'Checking availability...';

  @override
  String get renewBookingInfoMessage =>
      'Voucher and payment method can be selected on the next page';

  @override
  String get renewBookingContinueButton => 'Continue to Payment';

  @override
  String get renewBookingRoomNumber => 'Room No.';

  @override
  String get checkInDateLabel => 'Check-in Date';

  @override
  String get checkOutDateLabel => 'Check-out Date';

  @override
  String get selectDateHint => 'Select a date';

  @override
  String get autoFilledHint => 'Automatically filled';

  @override
  String get rentTypeLabel => 'Rental Type';

  @override
  String get dailyRentType => 'Daily';

  @override
  String get monthlyRentType => 'Monthly';

  @override
  String get durationLabel => 'Duration';

  @override
  String get dailyDurationLabel => 'Duration (Days)';

  @override
  String get dailyDurationHint => 'Enter days';

  @override
  String get monthlyDurationLabel => 'Duration (Months)';

  @override
  String get monthlyDurationHint => 'Enter months';

  @override
  String get durationHint => 'Select duration';

  @override
  String get roomCardAvailable => 'Available';

  @override
  String get roomCardNotAvailable => 'Not Available';

  @override
  String get roomCardUnderMaintenance => 'Under Maintenance';

  @override
  String get roomCardCurrentlyRented => 'Currently Rented';

  @override
  String get roomCardUnknown => 'Unknown';

  @override
  String roomCardStartingPrice(Object price) {
    return 'Starting from $price/Month';
  }

  @override
  String get dialogTermsTitle => 'TERMS AND CONDITIONS OF USE';

  @override
  String get dialogTermsAgree => 'I agree to the terms and conditions';

  @override
  String get dialogTermsContinueButton => 'Agree and Continue';

  @override
  String get dialogPrivacyTitle =>
      'PRIVACY POLICY AND PERSONAL DATA PROTECTION';

  @override
  String get dialogPrivacyAgree =>
      'I understand and agree to the Privacy Policy';

  @override
  String get paymentBookingSuccess => 'Booking successful';

  @override
  String get paymentBookingFailed => 'Booking failed';

  @override
  String get paymentError => 'Error:';

  @override
  String get paymentRentType => 'Rental Type';

  @override
  String get paymentDuration => 'Duration';

  @override
  String paymentDurationValue(num count, String rentType) {
    String _temp0 = intl.Intl.selectLogic(rentType, {
      'daily': 'Day',
      'monthly': 'Month',
      'other': 'Days',
    });
    return '$count $_temp0';
  }

  @override
  String get paymentCheckInDate => 'Check-in Date';

  @override
  String get paymentCheckOutDate => 'Check-out Date';

  @override
  String get paymentMethodTitle => 'Payment Method';

  @override
  String get paymentVoucherTitle => 'Voucher';

  @override
  String get paymentVoucherPlaceholder => 'Enter voucher code';

  @override
  String get paymentVoucherApplyButton => 'Apply';

  @override
  String get paymentVoucherApplied => 'Voucher applied';

  @override
  String get paymentVoucherInvalid => 'Invalid voucher code';

  @override
  String get paymentVoucherMyVouchers => 'My Vouchers';

  @override
  String get paymentVoucherRedeemCode => 'Redeem Code';

  @override
  String get paymentVoucherNoVouchers => 'No vouchers available';

  @override
  String get paymentVoucherUseButton => 'Use';

  @override
  String get paymentVoucherValidUntil => 'Valid until';

  @override
  String get paymentVoucherRedeemTitle => 'Enter Your Voucher Code';

  @override
  String get paymentVoucherRedeemDescription =>
      'Enter the code you received to get discount';

  @override
  String get paymentVoucherRedeemHint => 'e.g. PROMO2024';

  @override
  String get paymentPageTitle => 'Booking Details';

  @override
  String get paymentPriceDetails => 'Price Details';

  @override
  String get paymentDailyPrice => 'Daily Price';

  @override
  String get paymentMonthlyPrice => 'Monthly Price';

  @override
  String get paymentSubtotal => 'Subtotal';

  @override
  String get paymentFee => 'Admin Fee';

  @override
  String get paymentTotalPrice => 'Total Price';

  @override
  String get paymentBookNowButton => 'Book Now';

  @override
  String get paymentGenerateTransferVA => 'Generate Transfer VA';

  @override
  String get paymentVirtualAccountSelectBank => 'Virtual Account - Select Bank';

  @override
  String get paymentQRIS => 'QRIS';

  @override
  String get paymentQRISSubtitle => 'Pay with QR Code';

  @override
  String get paymentCreditCard => 'Credit Card';

  @override
  String get paymentCreditCardSubtitle => 'Visa, Mastercard, JCB';

  @override
  String get paymentManualTransferBRI => 'Manual VA Transfer BRI Bank';

  @override
  String get paymentVehicleDetailTitle => 'Vehicle Details';

  @override
  String get paymentVehiclePlateLabel => 'Vehicle Plate Number *';

  @override
  String get paymentVehiclePlateHint => 'EXAMPLE: B 1234 ABC';

  @override
  String get paymentVehiclePlateHelper => 'Required for vehicle parking';

  @override
  String get paymentWarningAgreeTerms => 'Check terms & conditions to continue';

  @override
  String get paymentWarningSelectPayment => 'Select payment method to continue';

  @override
  String get paymentWarningSelectBank => 'Select bank for Virtual Account';

  @override
  String get paymentWarningVehiclePlate =>
      'Fill in vehicle plate number for parking';

  @override
  String get paymentWarningCompleteData => 'Complete all data to continue';

  @override
  String get paymentTermsAgreePrefix =>
      'I declare that I have read, understood, and agree to the data, information and transaction details above, as well as the ';

  @override
  String get paymentTermsAnd => ', ';

  @override
  String get paymentTermsConditions => 'T&C';

  @override
  String get paymentPrivacyPolicy => 'Privacy Policy';

  @override
  String get paymentTermsAndRental => ', and ';

  @override
  String get paymentRentalAgreement => 'Rental Agreement';

  @override
  String get vaGenerationFailedTitle => 'VA Generation Failed';

  @override
  String get vaGenerationFailedMessage =>
      'Booking saved successfully, but failed to create Virtual Account.';

  @override
  String get vaGenerationFailedNote =>
      'You can try creating VA again from booking details page.';

  @override
  String get vaGenerationErrorTitle => 'VA Generation Error';

  @override
  String get vaGenerationErrorMessage =>
      'Booking saved successfully, but an error occurred while creating Virtual Account.';

  @override
  String get vaGenerationErrorNote =>
      'Please contact customer service or try again later.';

  @override
  String get qrisGenerationFailedTitle => 'QRIS Generation Failed';

  @override
  String get qrisGenerationFailedMessage =>
      'Booking saved successfully, but failed to create QRIS.';

  @override
  String get qrisGenerationFailedNote =>
      'You can try creating QRIS again from booking details page.';

  @override
  String get qrisGenerationErrorTitle => 'QRIS Generation Error';

  @override
  String get qrisGenerationErrorMessage =>
      'Booking saved successfully, but an error occurred while creating QRIS.';

  @override
  String get qrisGenerationErrorNote =>
      'Please contact customer service or try again later.';

  @override
  String get ccGenerationFailedTitle => 'CC Payment Failed';

  @override
  String get ccGenerationFailedMessage =>
      'Booking saved successfully, but failed to create Credit Card payment.';

  @override
  String get ccGenerationFailedNote =>
      'You can try creating payment again from booking details page.';

  @override
  String get ccGenerationErrorTitle => 'CC Payment Error';

  @override
  String get ccGenerationErrorMessage =>
      'Booking saved successfully, but an error occurred while processing Credit Card payment.';

  @override
  String get ccGenerationErrorNote =>
      'Please contact customer service or try again later.';

  @override
  String get vaResultDialogTitle => 'Virtual Account Successfully Created!';

  @override
  String get vaResultDialogBank => 'Bank';

  @override
  String get vaResultDialogVANumber => 'VA Number';

  @override
  String get vaResultDialogAmount => 'Amount';

  @override
  String get vaResultDialogValidUntil => 'Valid until';

  @override
  String get vaResultDialogHowToPayButton => 'View Payment Instructions';

  @override
  String get vaResultDialogOrderAgainButton => 'Order Again';

  @override
  String get vaResultDialogCloseButton => 'Close';

  @override
  String get vaResultDialogCopySuccess => 'VA number successfully copied';

  @override
  String get vaResultDialogLinkUnavailable =>
      'Payment instructions link unavailable';

  @override
  String get vaResultDialogLinkError => 'Cannot open payment instructions link';

  @override
  String get qrisResultDialogTitle => 'QRIS Successfully Created!';

  @override
  String get qrisResultDialogAmount => 'Amount';

  @override
  String get qrisResultDialogValidUntil => 'Valid until';

  @override
  String get qrisResultDialogScanQR => 'Scan QR Code';

  @override
  String get qrisResultDialogDownloadQR => 'Download QR Code';

  @override
  String get qrisResultDialogOrderAgainButton => 'Order Again';

  @override
  String get qrisResultDialogCloseButton => 'Close';

  @override
  String get qrisResultDialogDownloadSuccess =>
      'QR Code successfully downloaded';

  @override
  String get bookingDetailsParkingCar => 'Car Parking';

  @override
  String get bookingDetailsParkingMotorcycle => 'Motorcycle Parking';

  @override
  String bookingDetailsParkingDuration(int count) {
    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '$count Months',
      one: '1 Month',
    );
    return '$_temp0';
  }

  @override
  String get qrisResultDialogDownloadError => 'Failed to download QR Code';

  @override
  String get propertyDetailLocation => 'Location';

  @override
  String get propertyDetailNearbyLocations => 'Nearby Locations';

  @override
  String get roomFilterAllStatus => 'All Status';

  @override
  String get roomFilterAvailable => 'Available';

  @override
  String get roomFilterOccupied => 'Occupied';

  @override
  String get paymentAdditionalFees => 'Additional Fees';

  @override
  String get paymentDepositRequired => 'Deposit (Required)';

  @override
  String get paymentDepositNote => 'Deposit will be refunded at checkout';

  @override
  String get paymentCarParking => 'Car Parking';

  @override
  String get paymentMotorcycleParking => 'Motorcycle Parking';

  @override
  String get paymentParkingDurationTitle => 'Parking Duration';

  @override
  String get paymentTotalParkingFee => 'Total Parking Fee:';

  @override
  String get paymentSelectParking => 'Select Parking (Optional)';

  @override
  String paymentParkingFull(Object available, Object capacity) {
    return 'Full ($available/$capacity available)';
  }

  @override
  String paymentParkingAvailable(Object available, Object capacity) {
    return '$available/$capacity available';
  }

  @override
  String get paymentPerDay => 'day';

  @override
  String get paymentPerMonth => 'month';

  @override
  String paymentMaxDuration(Object duration, Object unit) {
    return 'max: $duration $unit';
  }

  @override
  String get confirmationDialogTitle => 'Confirm Booking';

  @override
  String get confirmationDialogBookingDetails => 'Booking Details';

  @override
  String get confirmationDialogProperty => 'Property';

  @override
  String get confirmationDialogRoom => 'Room';

  @override
  String get confirmationDialogRentType => 'Rental Type';

  @override
  String get confirmationDialogDuration => 'Duration';

  @override
  String get confirmationDialogCheckInDate => 'Check-in Date';

  @override
  String get confirmationDialogCheckOutDate => 'Check-out Date';

  @override
  String get confirmationDialogPriceDetails => 'Price Details';

  @override
  String get confirmationDialogDailyPrice => 'Daily Price';

  @override
  String get confirmationDialogMonthlyPrice => 'Monthly Price';

  @override
  String get confirmationDialogFee => 'Fee';

  @override
  String get confirmationDialogTotalPrice => 'Total Price';

  @override
  String get confirmationDialogConfirmationMessage =>
      'Are you sure about this booking and want to proceed with payment?';

  @override
  String get confirmationDialogCancelButton => 'Cancel';

  @override
  String get confirmationDialogConfirmButton => 'Agree';

  @override
  String get paymentMethodBankTransfer => 'Bank Transfer';

  @override
  String get paymentMethodCash => 'Pay on the Spot';

  @override
  String get adminFeeLabel => 'Admin Fee';

  @override
  String get dailyDurationUnit => 'Days';

  @override
  String get monthlyDurationUnit => 'Months';

  @override
  String get normalPriceLabel => '(Normal Price)';

  @override
  String get taxlabel => 'Service Fee';

  @override
  String get errorDisplayTitle => 'TEMPORARILY UNAVAILABLE';

  @override
  String get contactUsTitle => 'Contact Us';

  @override
  String get chatViaWhatsApp => 'Chat via WhatsApp';

  @override
  String get sendEmail => 'Send Email';

  @override
  String get cancelButtonLabel => 'Cancel';

  @override
  String get noInternetTitle => 'Connection Lost';

  @override
  String get noInternetMessage =>
      'Oops! It seems you are not connected to the internet. Please check your connection.';

  @override
  String get retryButton => 'OK';

  @override
  String get filtertitle => 'Search Filter';

  @override
  String get checkInDialogTitle => 'Check-In Confirmation';

  @override
  String get checkInSectionTitle => 'Check In';

  @override
  String get checkInDialogIdCardSection => 'ID Card Photo';

  @override
  String get checkInDialogUploadIdCard => 'Upload ID Card';

  @override
  String get checkInDialogTapToUpload => 'Tap to upload from camera or gallery';

  @override
  String get checkInDialogBookingDetails => 'Booking Details';

  @override
  String get checkInDialogPaymentProof => 'Payment Proof';

  @override
  String get checkInDialogTermsAgreement =>
      'I have read and agree to the Terms and Conditions and Privacy Policy';

  @override
  String get checkInDialogConfirmButton => 'Confirm Check-In';

  @override
  String get checkInDialogIdCardRequired => 'Please upload your ID card';

  @override
  String get checkInDialogTermsRequired =>
      'Please agree to the terms and conditions';

  @override
  String get comingSoonTitle1 => 'COMING';

  @override
  String get comingSoonTitle2 => 'SOON';

  @override
  String get comingSoonMessage =>
      'We\'re working hard to making\nsomething amazing, stay tune';

  @override
  String get profileAddressBookTitle => 'Address Book';

  @override
  String get profileAddressBookSubText => 'Manage your saved addresses';

  @override
  String get profileOrderHistoryTitle => 'Order History';

  @override
  String get profileOrderHistorySubText => 'View your past orders';

  @override
  String get profileLanguageTitle => 'Language';

  @override
  String get profileLanguageSubText => 'English';

  @override
  String get profileNotificationsTitle => 'Notifications';

  @override
  String get profileGetHelpTitle => 'Get Help';

  @override
  String get profilePrivacyPolicyTitle => 'Privacy Policy';

  @override
  String get profileTermsConditionsTitle => 'Terms & Conditions';

  @override
  String get roomSortAllRooms => 'All Rooms';

  @override
  String get roomSortFilterBy => 'Filter by';

  @override
  String get profileIdMissingWarning => 'ID document not uploaded';

  @override
  String get profileIdMissingDesc =>
      'Upload your ID Card, KITAS, or Passport for verification';

  @override
  String get profilePhoneMissingWarning => 'Phone number not added';

  @override
  String get profilePhoneMissingDesc =>
      'Add your phone number for easier communication';

  @override
  String get profileUploadIdTitle => 'Upload ID Document';

  @override
  String get profileUploadIdSubText => 'ID Card / KITAS / Passport';

  @override
  String get profileUploadIdInfo =>
      'Please upload a clear photo of your ID document (KTP, KITAS, or Passport) for identity verification';

  @override
  String get profileUploadIdNoImageSelected => 'No image selected';

  @override
  String get profileUploadIdSelectImage => 'Select Image';

  @override
  String get profileUploadIdChangeImage => 'Change Image';

  @override
  String get profileUploadIdUpload => 'Upload Document';

  @override
  String get profileUploadIdUploading => 'Uploading...';

  @override
  String get profileUploadIdChooseSource => 'Choose Image Source';

  @override
  String get profileUploadIdCamera => 'Camera';

  @override
  String get profileUploadIdGallery => 'Gallery';

  @override
  String get profileUploadIdSuccess => 'ID document uploaded successfully';

  @override
  String get profileUploadIdError => 'Failed to upload ID document';

  @override
  String get profileUploadIdErrorPick => 'Failed to select image';

  @override
  String get profileUploadIdNoImage => 'Please select an image first';

  @override
  String get profileUploadIdNotLoggedIn => 'You must be logged in to upload';

  @override
  String get profileUploadIdGuidelines => 'Upload Guidelines';

  @override
  String get profileUploadIdGuideline1 =>
      'Make sure the document is clear and readable';

  @override
  String get profileUploadIdGuideline2 =>
      'Avoid glare or shadows on the document';

  @override
  String get profileUploadIdGuideline3 =>
      'Ensure all text and photos are visible';

  @override
  String get profileUploadIdGuideline4 => 'File size should not exceed 5MB';

  @override
  String get profileDeactivateAccountTitle => 'Delete Account';

  @override
  String get profileDeactivateAccountSubText => 'Delete your account';

  @override
  String get profileDeactivateAccountConfirm =>
      'Are you sure you want to Delete your account? This action cannot be undone.';

  @override
  String get profileDeactivateAccountButton => 'Delete';

  @override
  String get profileDeactivateAccountSuccess => 'Account Delete successfully';

  @override
  String get profileDeactivateAccountError => 'Failed to Delete account';

  @override
  String get profileDeactivateAccountUserError =>
      'Unable to load user information. Please try again.';

  @override
  String get profileDeactivateAccountLoading => 'Delete account...';

  @override
  String get bottomBarStartingFrom => 'Starting from';

  @override
  String get bottomBarPerMonth => 'Month';

  @override
  String get bottomBarPerDay => 'Day';

  @override
  String get bottomBarContactUs => 'Contact Us';

  @override
  String get bottomBarRoomUnavailable => 'Room Unavailable';

  @override
  String get bottomBarContactCustomerService =>
      'Contact customer service for more information';

  @override
  String get bottomBarSubtotal => 'Subtotal';

  @override
  String get imageViewerClose => 'Close';

  @override
  String imageViewerImageCounter(int current, int total) {
    return 'Image $current of $total';
  }

  @override
  String get detailPropertyPageTitle => 'Property Details';

  @override
  String get roomDetailsPageTitle => 'Room Details';

  @override
  String get googleSignInNewAccountTitle => 'New Account Created';

  @override
  String get googleSignInNewAccountMessage =>
      'Your account has not been signed up yet.\n\nRegistration successful. Please check your email to verify your account.';

  @override
  String get googleSignInWelcomeBackTitle => 'Welcome Back';

  @override
  String get googleSignInWelcomeBackMessage =>
      'You have successfully signed in with Google!';

  @override
  String get pingSlowConnectionTitle => 'Slow Connection';

  @override
  String get pingSlowConnectionMessage =>
      'Your internet connection is slow. This may affect your experience.';

  @override
  String get pingNoConnectionTitle => 'No Internet Connection';

  @override
  String get pingNoConnectionMessage =>
      'Unable to connect to the internet. Please check your connection.';

  @override
  String get pingDialogOkButton => 'OK';

  @override
  String get chatRoomTitle => 'Chat';

  @override
  String get chatInputHint => 'Type a message...';

  @override
  String get chatSendButton => 'Send';

  @override
  String get chatSelectImage => 'Select Image';

  @override
  String get chatImagePreview => 'Image Preview';

  @override
  String get chatMessageEdited => 'Edited';

  @override
  String get chatNoMessages => 'No messages yet';

  @override
  String get chatStartConversation => 'Start a conversation';

  @override
  String get chatLoadingMessages => 'Loading messages...';

  @override
  String get chatErrorLoadingMessages => 'Failed to load messages';

  @override
  String get chatCreatingConversation => 'Creating conversation...';

  @override
  String get chatConversationCreated => 'Conversation created successfully';

  @override
  String get chatMessageSent => 'Message sent';

  @override
  String get chatMessageFailed => 'Failed to send message';

  @override
  String get chatImageTooLarge => 'Image size must be less than 10MB';

  @override
  String get chatInvalidBooking =>
      'You must have an active booking to start a conversation';

  @override
  String get chatDuplicateConversation => 'Conversation already exists';

  @override
  String get chatWithFrontOffice => 'Chat with Front Office';

  @override
  String get chatWithHeadOffice => 'Chat with Head Office';

  @override
  String get chatPickFromGallery => 'Gallery';

  @override
  String get chatPickFromCamera => 'Camera';

  @override
  String get chatCancel => 'Cancel';

  @override
  String get chatSending => 'Sending...';

  @override
  String get chatRetry => 'Retry';

  @override
  String get chatLoadMore => 'Load more messages';

  @override
  String get chatMarkAsRead => 'Mark as read';

  @override
  String get chatEditMessage => 'Edit message';

  @override
  String get chatDeleteMessage => 'Delete message';

  @override
  String get chatCopyMessage => 'Copy message';

  @override
  String get chatImageUploadError => 'Failed to upload image';

  @override
  String get chatNetworkError => 'Network error. Please check your connection.';

  @override
  String get chatServerError => 'Server error. Please try again later.';

  @override
  String get chatUnknownError => 'An unknown error occurred';

  @override
  String get chatImageFormatError => 'Only JPG and PNG images are allowed';

  @override
  String get chatImagePickError => 'Failed to select image. Please try again.';

  @override
  String get showAll => 'Show All';

  @override
  String get loadingPropertyName => 'Loading Property Name';

  @override
  String get loadingAddress => 'Loading Address';

  @override
  String get loadingDistance => 'Loading Distance';

  @override
  String get loadingType => 'Loading';

  @override
  String get loadingBestSellerProperty => 'Loading Best Seller Property';

  @override
  String get loadingBudgetProperty => 'Loading Budget Property';

  @override
  String get loadingRoomName => 'Loading Room Name';

  @override
  String get loadingDescription => 'Loading Description';

  @override
  String get loadingStatus => 'Loading Status';

  @override
  String get loadingRoomType => 'Room Type: Loading';

  @override
  String get loadingPrice => 'Price: Rp 0 / night';

  @override
  String get loadingPropertyType => 'Loading Property Type';

  @override
  String get loading => 'Loading';

  @override
  String get helpCenter => 'Help Center';

  @override
  String get faqTabLabel => 'FAQ';

  @override
  String get contactUsTabLabel => 'Contact Us';

  @override
  String get noActiveBookings => 'No Orders Yet';

  @override
  String get noActiveBookingsMessage =>
      'You don\'t have any orders yet. Start exploring our properties to find the perfect place!';

  @override
  String get browseProperties => 'Browse Properties';

  @override
  String get selectAppleAccount => 'Select Apple Account';

  @override
  String get selectAccountForLogin =>
      'Select the account you want to use to login:';

  @override
  String get verified => 'Verified';

  @override
  String get notVerified => 'Not Verified';

  @override
  String get useAnotherAppleAccount => 'Use another Apple account';

  @override
  String get switchAccount => 'Switch Account';

  @override
  String get addAccount => 'Add Account';

  @override
  String get addAnotherAccount => 'Add Another Account';

  @override
  String get removeButton => 'Remove';

  @override
  String get removeAccountDialogTitle => 'Remove Account';

  @override
  String get removeAccountTooltip => 'Remove account';

  @override
  String get accountRemovedSuccess => 'Account removed';

  @override
  String get failedToRemoveAccount => 'Failed to remove account';

  @override
  String get failedToSwitchAccount => 'Failed to switch account';

  @override
  String get noAccountsFound => 'No accounts found';

  @override
  String get signInWithAppleToAddAccount =>
      'Sign in with Apple to add an account';

  @override
  String get activeStatus => 'Active';

  @override
  String get vaGenerationFailed => 'VA Generation Failed';

  @override
  String get vaGenerationError =>
      'Booking saved successfully, but failed to create Virtual Account.';

  @override
  String get qrisGenerationFailed => 'QRIS Generation Failed';

  @override
  String get qrisGenerationError =>
      'Booking saved successfully, but failed to create QRIS.';

  @override
  String get ccPaymentFailed => 'CC Payment Failed';

  @override
  String get ccPaymentError =>
      'Booking saved successfully, but failed to process credit card payment.';

  @override
  String get errorDetail => 'Error Detail:';

  @override
  String get retryFromBookingDetail =>
      'You can try creating VA/QRIS again from the booking detail page.';

  @override
  String get goToMyBooking => 'Go to My Booking';

  @override
  String get indonesianLanguage => 'Indonesia';

  @override
  String get englishLanguage => 'English';

  @override
  String get registrationDisabled => 'Registration Unavailable';

  @override
  String get registrationDisabledMessage =>
      'Registration feature is temporarily disabled. Please try again later.';

  @override
  String get notLoggedIn => 'Not logged in';

  @override
  String get edited => 'Edited';

  @override
  String get greetingMorning => 'Good morning';

  @override
  String get greetingAfternoon => 'Good afternoon';

  @override
  String get greetingEvening => 'Good evening';

  @override
  String get greetingNight => 'Good night';

  @override
  String get homeSubtitle => 'Where do you want to stay today?';

  @override
  String get homeBannerTitle => 'Find Your Dream Home';

  @override
  String get homeBannerPart1 => 'Find Your ';

  @override
  String get homeBannerPart2 => 'Dream';

  @override
  String get homeBannerPart3 => ' Home';

  @override
  String get genderMale => 'Male';

  @override
  String get genderFemale => 'Female';

  @override
  String get genderMixed => 'Mixed';

  @override
  String get scanQRWithEwallet => 'Scan this QR code with your e-wallet app';

  @override
  String get downloadQR => 'Download QR Code';

  @override
  String get downloadQRSuccess => 'QR code saved to gallery';

  @override
  String get downloadQRFailed => 'Failed to save QR code';

  @override
  String get downloadQRPermissionDenied => 'Permission denied to save images';

  @override
  String get creditCardPayment => 'Credit Card Payment';

  @override
  String get continuePayment => 'Continue Payment';

  @override
  String get ccPaymentNote =>
      'Click the button above to complete your credit card payment';

  @override
  String get expiresIn => 'Expires in';

  @override
  String get darkModeLabel => 'Dark Mode';

  @override
  String get lightModeLabel => 'Light Mode';

  @override
  String get switchToLightMode => 'Switch to light mode';

  @override
  String get switchToDarkMode => 'Switch to dark mode';
}
