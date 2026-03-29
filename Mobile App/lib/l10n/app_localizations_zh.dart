// ignore: unused_import
import 'package:intl/intl.dart' as intl;
import 'app_localizations.dart';

// ignore_for_file: type=lint

/// The translations for Chinese (`zh`).
class AppLocalizationsZh extends AppLocalizations {
  AppLocalizationsZh([String locale = 'zh']) : super(locale);

  @override
  String get searchBannerTitle => '搜索';

  @override
  String get browseAll => '全部房源';

  @override
  String get categories => '分类';

  @override
  String get availableNow => '现在可用';

  @override
  String get filterCategoryAll => '全部';

  @override
  String get popularArea => '热门地区';

  @override
  String get budget => '附近房源';

  @override
  String get promotion => '优惠活动';

  @override
  String get promoBanner => '特别优惠';

  @override
  String get promoDetailTitle => '优惠详情';

  @override
  String get promoDescription => '优惠描述';

  @override
  String get noDescription => '暂无描述';

  @override
  String get promoHowToClaim => '如何领取';

  @override
  String get promoClaimStep1 => '选择您想要的房产';

  @override
  String get promoClaimStep2 => '然后选择您想要的房间';

  @override
  String get promoClaimStep3 => '在预订时输入优惠码';

  @override
  String get promoClaimStep4 => '完成预订';

  @override
  String get promoClaimStep5 => '享受您的优惠/折扣';

  @override
  String get promoTermsTitle => '条款与条件';

  @override
  String get promoTerm1 => '条款和条件适用';

  @override
  String get promoTerm2 => '不可与其他优惠同时使用';

  @override
  String get promoTerm3 => '优惠期限有限';

  @override
  String get promoTerm4 => '仅限部分用户使用';

  @override
  String get promoLoadError => '加载优惠详情失败';

  @override
  String get homeLabel => '首页';

  @override
  String get myBookingLabel => '我的订单';

  @override
  String get umLabel => 'UM';

  @override
  String get csLabel => '客服';

  @override
  String get profileLabel => '个人中心';

  @override
  String get popularAreaJakarta => '雅加达';

  @override
  String get detailJakarta => '都市商业和娱乐中心';

  @override
  String get popularAreaBogor => '茂物';

  @override
  String get detailBogor => '雨城，凉爽宜人的自然环境';

  @override
  String get welcomeTagline => '找到您的理想住所';

  @override
  String get welcomeMessage => '您的舒适之旅从这里开始';

  @override
  String get welcomeGetStartedButton => '开始使用';

  @override
  String get welcomeSignUpPrompt => '还没有账号？ ';

  @override
  String get welcomeSignUpButton => '注册';

  @override
  String get filterCategoryKos => '合租房';

  @override
  String get filterCategoryApartment => '公寓';

  @override
  String get filterCategoryHotel => '酒店';

  @override
  String get filterCategoryVilla => '别墅';

  @override
  String get filterLabelCategory => '类别';

  @override
  String get filterLabelRentType => '租赁类型';

  @override
  String get filterRentTypeDaily => '每日';

  @override
  String get filterRentTypeMonthly => '每月';

  @override
  String get filterLabelCheckIn => '入住日期';

  @override
  String get filterHintCheckIn => '选择日期';

  @override
  String get filterLabelDuration => '时长';

  @override
  String get filterHintDurationDays => '输入天数';

  @override
  String get filterHintDurationMonths => '输入月数';

  @override
  String get filterSuffixDays => '天';

  @override
  String get filterSuffixMonths => '月';

  @override
  String get filterLabelCheckOut => '退房日期';

  @override
  String get filterHintCheckOut => '自动填充';

  @override
  String get filterButtonSearch => '搜索';

  @override
  String get loginButton => '登录';

  @override
  String get registerButton => '立即注册';

  @override
  String get forgotPasswordTitle => '忘记密码';

  @override
  String get forgotPasswordSubtitle => '别担心，只需输入您的邮箱并创建新密码';

  @override
  String get emailInputHint => '输入邮箱';

  @override
  String get sendCodeButton => '发送验证码';

  @override
  String get rememberPasswordPrompt => '记得密码？';

  @override
  String get loginButtonText => '登录';

  @override
  String get emailEmptyError => '邮箱不能为空';

  @override
  String get emailInvalidError => '邮箱格式无效';

  @override
  String get passwordResetSuccess => '密码重置链接已发送到您的邮箱！';

  @override
  String get passwordResetError => '发送请求时出错。';

  @override
  String get availableStatus => '可预订';

  @override
  String get unavailableStatus => '不可预订';

  @override
  String get unknownStatus => '未知';

  @override
  String startingFrom(Object price) {
    return '起价 $price/月';
  }

  @override
  String get loginWelcomeTitle => '您好！欢迎回来';

  @override
  String get loginEmailHint => '输入您的邮箱';

  @override
  String get loginPasswordHint => '输入您的密码';

  @override
  String get loginEmptyError => '部分必填字段不能为空';

  @override
  String get loginInvalidFormatError => '您输入的登录地址无效。请使用有效的邮箱或手机号格式。';

  @override
  String get loginPasswordLengthError => '您的密码必须至少为8个字符。';

  @override
  String get loginIncorrectCredentials => '邮箱/手机号和密码必须匹配。';

  @override
  String get loginUnknownError => '登录时发生错误。';

  @override
  String get loginUnknownNetworkError => '发生未知网络错误。';

  @override
  String loginGeneralError(Object error) {
    return '发生错误：$error';
  }

  @override
  String get loginOrText => '或';

  @override
  String get loginNoAccountPrompt => '还没有账号？';

  @override
  String get loginAsGuestButton => '以访客身份继续？';

  @override
  String get rememberMe => '记住我';

  @override
  String get biometricAuthFailed => '生物识别验证失败。请重试。';

  @override
  String get biometricAuthNotConfigured => '生物识别验证尚未配置。请先手动登录并勾选记住我。';

  @override
  String get emailNotVerifiedTitle => '邮箱未验证';

  @override
  String get emailNotVerifiedMessage => '请先验证您的邮箱以继续。请检查您的邮箱收件箱并点击我们发送给您的验证链接。';

  @override
  String get accountDeactivatedTitle => 'Akun Dinonaktifkan';

  @override
  String get accountDeactivatedMessage =>
      'Akun Anda telah dinonaktifkan. Silakan hubungi support untuk informasi lebih lanjut.';

  @override
  String get registerWelcomeTitle => '您好！注册开始使用';

  @override
  String get registerFormError => '请正确填写所有必填字段。';

  @override
  String get registerSuccessMessage => '注册成功，请验证您的邮箱并重新登录';

  @override
  String get registerUnknownError => '注册时发生未知错误。';

  @override
  String get registersuccessnotif => '注册成功，请验证您的邮箱并重新登录';

  @override
  String get firstNameLabel => '名字';

  @override
  String get firstNameEmptyError => '名字不能为空';

  @override
  String get lastNameLabel => '姓氏';

  @override
  String get lastNameEmptyError => '姓氏不能为空';

  @override
  String get registerOneNameLabel => '我只有名字';

  @override
  String get fullNameLabel => '全名';

  @override
  String get nameEmptyError => '全名为必填项';

  @override
  String get usernameLabel => '用户名';

  @override
  String get usernameEmptyError => '用户名不能为空';

  @override
  String get emailLabel => '邮箱';

  @override
  String get phoneNumberLabel => '手机号';

  @override
  String get phoneNumberHint => '输入手机号';

  @override
  String get phoneNumberEmptyError => '手机号不能为空';

  @override
  String get passwordLabel => '密码';

  @override
  String get passwordEmptyError => '密码不能为空';

  @override
  String get passwordLengthError => '密码必须至少为8个字符。';

  @override
  String get passwordLengthInfo => '密码必须至少为8个字符。';

  @override
  String get confirmPasswordLabel => '确认密码';

  @override
  String get confirmPasswordEmptyError => '确认密码不能为空';

  @override
  String get passwordMismatchError => '密码和确认密码不匹配。';

  @override
  String get confirmPasswordInfo => '重新输入相同的密码进行确认。';

  @override
  String get registerOrText => '或使用以下方式注册';

  @override
  String get registerHaveAccountPrompt => '已有账号？';

  @override
  String get updatePasswordTitle => '更新您的密码';

  @override
  String get updatePasswordSuccessMessage => '密码更新成功！';

  @override
  String get updatePasswordFormError => '请正确填写所有字段。';

  @override
  String get updatePasswordUserIdError => '错误：未找到用户ID。请重新登录。';

  @override
  String get oldPasswordLabel => '旧密码';

  @override
  String get oldPasswordEmptyError => '旧密码不能为空';

  @override
  String get newPasswordLabel => '新密码';

  @override
  String get newPasswordEmptyError => '新密码不能为空';

  @override
  String get newPasswordLengthError => '密码必须至少为8个字符。';

  @override
  String get newPasswordLengthInfo => '新密码必须至少为8个字符。';

  @override
  String get confirmNewPasswordLabel => '确认新密码';

  @override
  String get confirmNewPasswordEmptyError => '确认新密码不能为空';

  @override
  String get newPasswordMismatchError => '密码确认不匹配。';

  @override
  String get confirmNewPasswordInfo => '重新输入相同的新密码进行确认。';

  @override
  String get updatePasswordButton => '更新密码';

  @override
  String get profileTitle => '个人中心';

  @override
  String get profileDefaultUsername => '用户';

  @override
  String get profileDefaultEmail => 'user@example.com';

  @override
  String get profileWelcomeText => '欢迎';

  @override
  String get profileUserProfileMenu => '用户资料';

  @override
  String get changePasswordTitle => '修改密码';

  @override
  String get changePasswordSubText => '更新您的账户密码';

  @override
  String get profileContactUsMenu => '联系我们';

  @override
  String get profileDeactivateAccountMenu => '停用账户';

  @override
  String get logoutTitle => '退出登录';

  @override
  String get logoutSubText => '从账户退出登录';

  @override
  String get confirmLogoutMessage => '您确定要退出此账户吗？';

  @override
  String get cancelButton => '取消';

  @override
  String get logoutButton => '退出登录';

  @override
  String get logoutErrorMessage => '退出登录失败';

  @override
  String get additionalSectionTitle => '其他';

  @override
  String get helpCenterTitle => '帮助中心';

  @override
  String get aboutTitle => '关于';

  @override
  String get deactivateAccountTitle => '删除账户';

  @override
  String get deactivateAccountSubText => '停用您的账户';

  @override
  String get confirmDeactivateAccountMessage => '您确定要停用您的账户吗？此操作无法撤销。';

  @override
  String get deactivateButton => '停用';

  @override
  String get deactivateSuccessMessage => '账户已成功停用。';

  @override
  String get deactivateErrorMessage => '停用账户失败。';

  @override
  String get deactivateUserIdNotFoundError => '未找到用户ID';

  @override
  String get deactivateAccountFailedError => '停用账户失败';

  @override
  String get deactivateAccountUnknownError => '未知错误';

  @override
  String get updateProfileTitle => '更新资料';

  @override
  String get updateProfileSuccessMessage => '资料更新成功';

  @override
  String get profileImageLoadError => '加载头像失败';

  @override
  String get uploadProfilePhotoPrompt => '点击上传头像';

  @override
  String get pickImageSourceTitle => '选择图片来源';

  @override
  String get cameraOption => '相机';

  @override
  String get galleryOption => '相册';

  @override
  String get firstNameHint => '您的名字';

  @override
  String get lastNameHint => '您的姓氏';

  @override
  String get usernameHint => '您的用户名';

  @override
  String get emailHint => '您的邮箱';

  @override
  String get updateProfileButton => '更新资料';

  @override
  String get myBookingTitle => '我的订单';

  @override
  String get upcomingTab => '即将到来';

  @override
  String get completedTab => '已完成';

  @override
  String get errorLoadingData => '加载数据出错';

  @override
  String get myBookingEmptyTitle => '暂无预订';

  @override
  String get myBookingEmptyMessage => '您还没有任何预订。开始探索我们的房源，找到您的理想住所！';

  @override
  String get myBookingBrowseProperties => '浏览房源';

  @override
  String get csTitle => '客户服务';

  @override
  String get csNoBookings => '暂无活动订单';

  @override
  String get csNoBookingsDesc => '您没有任何活动订单可以咨询。请先预订房源。';

  @override
  String get csSelectRecipient => '选择接收人';

  @override
  String get csFrontOffice => '前台';

  @override
  String get csHeadOffice => '总部财务';

  @override
  String get csHeadOfficeDesc => '财务和付款咨询';

  @override
  String get backToHome => '返回首页';

  @override
  String get tryAgain => '重试';

  @override
  String get cancel => '取消';

  @override
  String get checkInLabel => '入住';

  @override
  String get checkOutLabel => '退房';

  @override
  String get myBookingDetailSelectImageFirst => 'ℹ️ 请先选择图片';

  @override
  String get myBookingDetailUploadSuccess => '✅ 图片上传成功';

  @override
  String get myBookingDetailUploadFailed => '❌ 图片上传失败';

  @override
  String get myBookingDetailDataNotFound => '未找到预订数据。';

  @override
  String get myBookingDetailPropertyNameDefault => '房源名称';

  @override
  String get myBookingDetailRoomNameDefault => '房间名称';

  @override
  String get myBookingDetailPaymentProofWarningTitle => '付款凭证尚未上传';

  @override
  String get myBookingDetailPaymentProofWarningMessage => '请上传您的付款凭证以避免预订被取消。';

  @override
  String get myBookingDetailTitle => '预订详情';

  @override
  String get myBookingDetailOrderId => '订单ID：';

  @override
  String get myBookingDetailPhoneNumber => '手机号：';

  @override
  String get myBookingDetailBookingType => '预订类型：';

  @override
  String get myBookingDetailDuration => '时长：';

  @override
  String get myBookingDetailTimeTitle => '时间详情';

  @override
  String get myBookingDetailCheckIn => '入住日期：';

  @override
  String get myBookingDetailCheckOut => '退房日期：';

  @override
  String get myBookingDetailBookingPriceTitle => '预订价格';

  @override
  String get myBookingDetailPricePerDay => '每日价格：';

  @override
  String get myBookingDetailPricePerMonth => '每月价格：';

  @override
  String get myBookingDetailSubtotal => '小计：';

  @override
  String get myBookingDetailSubtotalBeforeDiscount => '折扣前小计：';

  @override
  String get myBookingDetailVoucher => '优惠券';

  @override
  String get myBookingDetailDeposit => '押金：';

  @override
  String get myBookingDetailNumberOfDays => '天数：';

  @override
  String get myBookingDetailNumberOfMonths => '月数：';

  @override
  String get myBookingDetailTotalPriceTitle => '总价';

  @override
  String get myBookingDetailServiceFee => '服务费：';

  @override
  String get myBookingDetailGrandtotal => '总计支付：';

  @override
  String get myBookingDetailPaymentProofTitle => '付款凭证';

  @override
  String get myBookingDetailPickFromGallery => '从相册选择';

  @override
  String get myBookingDetailTakePhoto => '拍照';

  @override
  String get myBookingDetailUploading => '正在上传付款凭证...';

  @override
  String get myBookingDetailUploadPaymentProof => '上传付款凭证';

  @override
  String get myBookingDetailUploadThisImage => '上传此图片';

  @override
  String get myBookingDetailError => '错误';

  @override
  String get myBookingDetailAppBarTitle => '我的订单详情';

  @override
  String get cameraNotFoundError => '未找到相机。';

  @override
  String get cameraInitFailed => '初始化相机失败';

  @override
  String get cameraUnexpectedError => '发生意外错误';

  @override
  String get cameraSelectFirstError => '错误：请先选择相机。';

  @override
  String get cameraCaptureFailed => '拍摄图片失败';

  @override
  String get cameraPopupTitle => '上传付款凭证';

  @override
  String get cameraGalleryTooltip => '从相册选择';

  @override
  String get cameraTryAgain => '重试';

  @override
  String get cameraGalleryButton => '从相册选择';

  @override
  String get cameraRetake => '重新拍摄';

  @override
  String get cameraUseThisImage => '使用此图片';

  @override
  String get viewerHideAttachment => '隐藏附件';

  @override
  String get viewerShowPaymentProof => '查看付款凭证';

  @override
  String get viewerUpdatePaymentProof => '更新付款凭证';

  @override
  String get propertyTypePageTitle => '探索房源类型';

  @override
  String get propertyTypeNoActiveFound => '未找到活动房源类型。';

  @override
  String get propertyTypeFailedToLoad => '加载房源类型失败';

  @override
  String get searchResultTitle => '搜索结果';

  @override
  String get searchResultFailedToLoad => '加载结果失败';

  @override
  String get detailPropertyMonth => '月';

  @override
  String get detailPropertyError => '错误';

  @override
  String get detailPropertyNameNotAvailable => '名称不可用';

  @override
  String get detailPropertyTagNotAvailable => '标签不可用';

  @override
  String detailPropertyFloorCount(num count) {
    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '$count层',
      one: '1层',
    );
    return '$_temp0';
  }

  @override
  String get detailPropertyFacilitiesTitle => '房源设施';

  @override
  String get detailPropertyNoFacilities => '无可用设施。';

  @override
  String get roomTypeAvailableRooms => '可预订房间';

  @override
  String get roomTypeNoRoomsAvailable => '暂无可用房间';

  @override
  String get contactBarPriceLabel => '起价';

  @override
  String get contactBarSafetyLabel => '优惠';

  @override
  String get contactBarLoginRequired => '请先登录以继续支付。';

  @override
  String get contactBarLoginButton => '登录';

  @override
  String get contactBarProfileRequired => '请先填写您的个人信息以继续支付。';

  @override
  String get contactBarProfileButton => '资料';

  @override
  String get contactBarPhoneRequired => '请先添加您的手机号以继续预订。';

  @override
  String get contactBarPhoneButton => '添加号码';

  @override
  String get contactBarIncompleteOrder => '请完成所有预订数据。';

  @override
  String get contactBarBookNowButton => '立即预订';

  @override
  String get contactBarOtherPropertiesButton => '其他房源';

  @override
  String get roomDetailsError => '错误';

  @override
  String get roomDetailsLoading => '正在加载房间详情...';

  @override
  String get roomDetailsDaily => '天';

  @override
  String get roomDetailsMonthly => '月';

  @override
  String get roomDetailsCompleteForm => '请完成所有预订数据。';

  @override
  String get roomDetailsPerMonth => '/月';

  @override
  String get roomDetailsPerDay => '/天';

  @override
  String get roomDetailsFloor => '楼层 ';

  @override
  String get roomDetailsArea => '面积 ';

  @override
  String get roomDetailsCapacity => '容量 ';

  @override
  String get roomBookinginfoTitle => '预订信息';

  @override
  String roomDetailsCapacityCount(num count) {
    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '$count人',
      one: '1人',
    );
    return '$_temp0';
  }

  @override
  String get roomDetailsBed => '床位 ';

  @override
  String get roomDetailsFacilitiesTitle => '房间设施';

  @override
  String get roomDetailsNoFacilities => '无可用设施';

  @override
  String get roomDetailsCheckingAvailability => '正在检查可用性...';

  @override
  String get roomDetailsFailedToCheckAvailability => '检查可用性失败。';

  @override
  String get roomDetailsRoomAvailable => '房间可预订';

  @override
  String get roomDetailsRoomNotAvailable => '这些日期房间不可预订';

  @override
  String get roomDetailsRoomStatusNotAvailable => '房间不可预订';

  @override
  String get roomDetailsRoomStatusUnderMaintenance => '房间维护中';

  @override
  String get roomDetailsRoomStatusCurrentlyRented => '房间已出租';

  @override
  String get roomDetailsRoomStatusOccupied => '房间已占用';

  @override
  String get roomDetailsRoomStatusCannotBook => '房间无法预订';

  @override
  String get roomDetailsLoginRequired => '您必须登录才能预订';

  @override
  String get roomDetailsProfilePictureRequired => '请完成您的身份证件上传以预订';

  @override
  String get roomDetailsAdditionalFeesTitle => '附加费用';

  @override
  String get roomDetailsDepositFee => '押金';

  @override
  String get roomDetailsDepositNote => '押金将在退房时退还';

  @override
  String get roomDetailsParkingCar => '汽车停车位';

  @override
  String get roomDetailsParkingMotorcycle => '摩托车停车位';

  @override
  String get roomDetailsParkingOptional => '可选';

  @override
  String get renewBookingTitle => '续订预订';

  @override
  String get renewBookingButton => '续订预订';

  @override
  String get renewBookingPeriodLabel => '周期';

  @override
  String get renewBookingPeriodDaily => '每日';

  @override
  String get renewBookingPeriodMonthly => '每月';

  @override
  String get renewBookingDurationLabel => '时长';

  @override
  String get renewBookingDurationDay => '天';

  @override
  String get renewBookingDurationMonth => '月';

  @override
  String get renewBookingCheckInLabel => '入住日期';

  @override
  String get renewBookingCheckOutLabel => '退房日期';

  @override
  String get renewBookingRoomAvailable => '房间可预订';

  @override
  String get renewBookingRoomNotAvailable => '这些日期房间不可预订';

  @override
  String get renewBookingCheckingAvailability => '正在检查可用性...';

  @override
  String get renewBookingInfoMessage => '优惠券和付款方式可在下一页选择';

  @override
  String get renewBookingContinueButton => '继续支付';

  @override
  String get renewBookingRoomNumber => '房间号';

  @override
  String get checkInDateLabel => '入住日期';

  @override
  String get checkOutDateLabel => '退房日期';

  @override
  String get selectDateHint => '选择日期';

  @override
  String get autoFilledHint => '自动填充';

  @override
  String get rentTypeLabel => '租赁类型';

  @override
  String get dailyRentType => '每日';

  @override
  String get monthlyRentType => '每月';

  @override
  String get durationLabel => '时长';

  @override
  String get dailyDurationLabel => '时长（天）';

  @override
  String get dailyDurationHint => '输入天数';

  @override
  String get monthlyDurationLabel => '时长（月）';

  @override
  String get monthlyDurationHint => '输入月数';

  @override
  String get durationHint => '选择时长';

  @override
  String get roomCardAvailable => '可预订';

  @override
  String get roomCardNotAvailable => '不可预订';

  @override
  String get roomCardUnderMaintenance => '维护中';

  @override
  String get roomCardCurrentlyRented => '已出租';

  @override
  String get roomCardUnknown => '未知';

  @override
  String roomCardStartingPrice(Object price) {
    return '起价 $price/月';
  }

  @override
  String get dialogTermsTitle => '使用条款和条件';

  @override
  String get dialogTermsAgree => '我同意条款和条件';

  @override
  String get dialogTermsContinueButton => '同意并继续';

  @override
  String get dialogPrivacyTitle => '隐私政策和个人数据保护';

  @override
  String get dialogPrivacyAgree => '我理解并同意隐私政策';

  @override
  String get paymentBookingSuccess => '预订成功';

  @override
  String get paymentBookingFailed => '预订失败';

  @override
  String get paymentError => '错误：';

  @override
  String get paymentRentType => '租赁类型';

  @override
  String get paymentDuration => '时长';

  @override
  String paymentDurationValue(num count, String rentType) {
    String _temp0 = intl.Intl.selectLogic(rentType, {
      'daily': '天',
      'monthly': '月',
      'other': '天',
    });
    return '$count $_temp0';
  }

  @override
  String get paymentCheckInDate => '入住日期';

  @override
  String get paymentCheckOutDate => '退房日期';

  @override
  String get paymentMethodTitle => '付款方式';

  @override
  String get paymentVoucherTitle => '优惠券';

  @override
  String get paymentVoucherPlaceholder => '输入优惠券代码';

  @override
  String get paymentVoucherApplyButton => '应用';

  @override
  String get paymentVoucherApplied => '优惠券已应用';

  @override
  String get paymentVoucherInvalid => '优惠券代码无效';

  @override
  String get paymentVoucherMyVouchers => '我的优惠券';

  @override
  String get paymentVoucherRedeemCode => '兑换代码';

  @override
  String get paymentVoucherNoVouchers => '暂无可用优惠券';

  @override
  String get paymentVoucherUseButton => '使用';

  @override
  String get paymentVoucherValidUntil => '有效期至';

  @override
  String get paymentVoucherRedeemTitle => '输入您的优惠券代码';

  @override
  String get paymentVoucherRedeemDescription => '输入您收到的代码以获得折扣';

  @override
  String get paymentVoucherRedeemHint => '例如：PROMO2024';

  @override
  String get paymentPageTitle => '预订详情';

  @override
  String get paymentPriceDetails => '价格详情';

  @override
  String get paymentDailyPrice => '每日价格';

  @override
  String get paymentMonthlyPrice => '每月价格';

  @override
  String get paymentSubtotal => '小计';

  @override
  String get paymentFee => '管理费';

  @override
  String get paymentTotalPrice => '总价';

  @override
  String get paymentBookNowButton => '立即预订';

  @override
  String get paymentGenerateTransferVA => '生成虚拟账户转账';

  @override
  String get paymentVirtualAccountSelectBank => '虚拟账户 - 选择银行';

  @override
  String get paymentQRIS => 'QRIS扫码支付';

  @override
  String get paymentQRISSubtitle => '使用二维码支付';

  @override
  String get paymentCreditCard => '信用卡';

  @override
  String get paymentCreditCardSubtitle => 'Visa、Mastercard、JCB';

  @override
  String get paymentManualTransferBRI => 'BRI银行手动虚拟账户转账';

  @override
  String get paymentVehicleDetailTitle => '车辆详情';

  @override
  String get paymentVehiclePlateLabel => '车牌号 *';

  @override
  String get paymentVehiclePlateHint => '例如：B 1234 ABC';

  @override
  String get paymentVehiclePlateHelper => '车辆停车所需';

  @override
  String get paymentWarningAgreeTerms => '请勾选条款和条件以继续';

  @override
  String get paymentWarningSelectPayment => '请选择付款方式以继续';

  @override
  String get paymentWarningSelectBank => '请为虚拟账户选择银行';

  @override
  String get paymentWarningVehiclePlate => '请填写停车的车牌号';

  @override
  String get paymentWarningCompleteData => '请完成所有数据以继续';

  @override
  String get paymentTermsAgreePrefix => '我同意';

  @override
  String get paymentTermsAnd => '和';

  @override
  String get paymentTermsConditions => '条款和条件';

  @override
  String get paymentPrivacyPolicy => '隐私政策';

  @override
  String get vaGenerationFailedTitle => '虚拟账户生成失败';

  @override
  String get vaGenerationFailedMessage => '预订已成功保存，但创建虚拟账户失败。';

  @override
  String get vaGenerationFailedNote => '您可以从预订详情页面重新尝试创建虚拟账户。';

  @override
  String get vaGenerationErrorTitle => '虚拟账户生成错误';

  @override
  String get vaGenerationErrorMessage => '预订已成功保存，但创建虚拟账户时发生错误。';

  @override
  String get vaGenerationErrorNote => '请联系客户服务或稍后重试。';

  @override
  String get qrisGenerationFailedTitle => 'QRIS生成失败';

  @override
  String get qrisGenerationFailedMessage => '预订已成功保存，但创建QRIS失败。';

  @override
  String get qrisGenerationFailedNote => '您可以从预订详情页面重新尝试创建QRIS。';

  @override
  String get qrisGenerationErrorTitle => 'QRIS生成错误';

  @override
  String get qrisGenerationErrorMessage => '预订已成功保存，但创建QRIS时发生错误。';

  @override
  String get qrisGenerationErrorNote => '请联系客户服务或稍后重试。';

  @override
  String get ccGenerationFailedTitle => '信用卡支付失败';

  @override
  String get ccGenerationFailedMessage => '预订已成功保存，但创建信用卡支付失败。';

  @override
  String get ccGenerationFailedNote => '您可以从预订详情页面重新尝试创建支付。';

  @override
  String get ccGenerationErrorTitle => '信用卡支付错误';

  @override
  String get ccGenerationErrorMessage => '预订已成功保存，但处理信用卡支付时发生错误。';

  @override
  String get ccGenerationErrorNote => '请联系客户服务或稍后重试。';

  @override
  String get vaResultDialogTitle => '虚拟账户创建成功！';

  @override
  String get vaResultDialogBank => '银行';

  @override
  String get vaResultDialogVANumber => '虚拟账户号';

  @override
  String get vaResultDialogAmount => '金额';

  @override
  String get vaResultDialogValidUntil => '有效期至';

  @override
  String get vaResultDialogHowToPayButton => '查看支付说明';

  @override
  String get vaResultDialogOrderAgainButton => '再次下单';

  @override
  String get vaResultDialogCloseButton => '关闭';

  @override
  String get vaResultDialogCopySuccess => '虚拟账户号已成功复制';

  @override
  String get vaResultDialogLinkUnavailable => '支付说明链接不可用';

  @override
  String get vaResultDialogLinkError => '无法打开支付说明链接';

  @override
  String get qrisResultDialogTitle => 'QRIS创建成功！';

  @override
  String get qrisResultDialogAmount => '金额';

  @override
  String get qrisResultDialogValidUntil => '有效期至';

  @override
  String get qrisResultDialogScanQR => '扫描二维码';

  @override
  String get qrisResultDialogDownloadQR => '下载二维码';

  @override
  String get qrisResultDialogOrderAgainButton => '再次下单';

  @override
  String get qrisResultDialogCloseButton => '关闭';

  @override
  String get qrisResultDialogDownloadSuccess => '二维码下载成功';

  @override
  String get bookingDetailsParkingCar => '汽车停车位';

  @override
  String get bookingDetailsParkingMotorcycle => '摩托车停车位';

  @override
  String bookingDetailsParkingDuration(int count) {
    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '$count个月',
      one: '1个月',
    );
    return '$_temp0';
  }

  @override
  String get qrisResultDialogDownloadError => '下载二维码失败';

  @override
  String get propertyDetailLocation => '位置';

  @override
  String get propertyDetailNearbyLocations => '附近地点';

  @override
  String get roomFilterAllStatus => '全部状态';

  @override
  String get roomFilterAvailable => '可预订';

  @override
  String get roomFilterOccupied => '已占用';

  @override
  String get paymentAdditionalFees => '附加费用';

  @override
  String get paymentDepositRequired => '押金（必需）';

  @override
  String get paymentDepositNote => '押金将在退房时退还';

  @override
  String get paymentCarParking => '汽车停车位';

  @override
  String get paymentMotorcycleParking => '摩托车停车位';

  @override
  String get paymentParkingDurationTitle => '停车时长';

  @override
  String get paymentTotalParkingFee => '停车费总计：';

  @override
  String get paymentSelectParking => '选择停车位（可选）';

  @override
  String paymentParkingFull(Object available, Object capacity) {
    return '已满（$available/$capacity可用）';
  }

  @override
  String paymentParkingAvailable(Object available, Object capacity) {
    return '$available/$capacity可用';
  }

  @override
  String get paymentPerDay => '天';

  @override
  String get paymentPerMonth => '月';

  @override
  String paymentMaxDuration(Object duration, Object unit) {
    return '最多：$duration$unit';
  }

  @override
  String get confirmationDialogTitle => '确认预订';

  @override
  String get confirmationDialogBookingDetails => '预订详情';

  @override
  String get confirmationDialogProperty => '房源';

  @override
  String get confirmationDialogRoom => '房间';

  @override
  String get confirmationDialogRentType => '租赁类型';

  @override
  String get confirmationDialogDuration => '时长';

  @override
  String get confirmationDialogCheckInDate => '入住日期';

  @override
  String get confirmationDialogCheckOutDate => '退房日期';

  @override
  String get confirmationDialogPriceDetails => '价格详情';

  @override
  String get confirmationDialogDailyPrice => '每日价格';

  @override
  String get confirmationDialogMonthlyPrice => '每月价格';

  @override
  String get confirmationDialogFee => '费用';

  @override
  String get confirmationDialogTotalPrice => '总价';

  @override
  String get confirmationDialogConfirmationMessage => '您确定此预订并想继续支付吗？';

  @override
  String get confirmationDialogCancelButton => '取消';

  @override
  String get confirmationDialogConfirmButton => '同意';

  @override
  String get paymentMethodBankTransfer => '银行转账';

  @override
  String get paymentMethodCash => '到店支付';

  @override
  String get adminFeeLabel => '管理费';

  @override
  String get dailyDurationUnit => '天';

  @override
  String get monthlyDurationUnit => '月';

  @override
  String get normalPriceLabel => '（正常价格）';

  @override
  String get taxlabel => '服务费';

  @override
  String get errorDisplayTitle => '暂时不可用';

  @override
  String get contactUsTitle => '联系我们';

  @override
  String get chatViaWhatsApp => '通过WhatsApp聊天';

  @override
  String get sendEmail => '发送邮件';

  @override
  String get cancelButtonLabel => '取消';

  @override
  String get noInternetTitle => '连接丢失';

  @override
  String get noInternetMessage => '糟糕！您似乎未连接到互联网。请检查您的连接。';

  @override
  String get retryButton => '确定';

  @override
  String get filtertitle => '搜索筛选';

  @override
  String get checkInDialogTitle => '入住确认';

  @override
  String get checkInSectionTitle => '入住';

  @override
  String get checkInDialogIdCardSection => '身份证照片';

  @override
  String get checkInDialogUploadIdCard => '上传身份证';

  @override
  String get checkInDialogTapToUpload => '点击从相机或相册上传';

  @override
  String get checkInDialogBookingDetails => '预订详情';

  @override
  String get checkInDialogPaymentProof => '付款凭证';

  @override
  String get checkInDialogTermsAgreement => '我已阅读并同意条款和条件以及隐私政策';

  @override
  String get checkInDialogConfirmButton => '确认入住';

  @override
  String get checkInDialogIdCardRequired => '请上传您的身份证';

  @override
  String get checkInDialogTermsRequired => '请同意条款和条件';

  @override
  String get comingSoonTitle1 => '即将';

  @override
  String get comingSoonTitle2 => '推出';

  @override
  String get comingSoonMessage => '我们正在努力打造\n令人惊叹的内容，敬请期待';

  @override
  String get profileAddressBookTitle => '地址簿';

  @override
  String get profileAddressBookSubText => '管理您保存的地址';

  @override
  String get profileOrderHistoryTitle => '订单历史';

  @override
  String get profileOrderHistorySubText => '查看您的过往订单';

  @override
  String get profileLanguageTitle => '语言';

  @override
  String get profileLanguageSubText => '简体中文';

  @override
  String get profileNotificationsTitle => '通知';

  @override
  String get profileGetHelpTitle => '获取帮助';

  @override
  String get profilePrivacyPolicyTitle => '隐私政策';

  @override
  String get profileTermsConditionsTitle => '条款和条件';

  @override
  String get roomSortAllRooms => '全部房间';

  @override
  String get roomSortFilterBy => '筛选方式';

  @override
  String get profileIdMissingWarning => '身份证件未上传';

  @override
  String get profileIdMissingDesc => '请上传您的身份证、KITAS或护照以进行验证';

  @override
  String get profilePhoneMissingWarning => '手机号未添加';

  @override
  String get profilePhoneMissingDesc => '添加您的手机号以便更方便地沟通';

  @override
  String get profileUploadIdTitle => '上传身份证件';

  @override
  String get profileUploadIdSubText => '身份证 / KITAS / 护照';

  @override
  String get profileUploadIdInfo => '请上传清晰的身份证件照片（KTP、KITAS或护照）以进行身份验证';

  @override
  String get profileUploadIdNoImageSelected => '未选择图片';

  @override
  String get profileUploadIdSelectImage => '选择图片';

  @override
  String get profileUploadIdChangeImage => '更换图片';

  @override
  String get profileUploadIdUpload => '上传证件';

  @override
  String get profileUploadIdUploading => '正在上传...';

  @override
  String get profileUploadIdChooseSource => '选择图片来源';

  @override
  String get profileUploadIdCamera => '相机';

  @override
  String get profileUploadIdGallery => '相册';

  @override
  String get profileUploadIdSuccess => '身份证件上传成功';

  @override
  String get profileUploadIdError => '上传身份证件失败';

  @override
  String get profileUploadIdErrorPick => '选择图片失败';

  @override
  String get profileUploadIdNoImage => '请先选择图片';

  @override
  String get profileUploadIdNotLoggedIn => '您必须登录才能上传';

  @override
  String get profileUploadIdGuidelines => '上传指南';

  @override
  String get profileUploadIdGuideline1 => '确保证件清晰可读';

  @override
  String get profileUploadIdGuideline2 => '避免证件上有眩光或阴影';

  @override
  String get profileUploadIdGuideline3 => '确保所有文字和照片清晰可见';

  @override
  String get profileUploadIdGuideline4 => '文件大小不应超过5MB';

  @override
  String get profileDeactivateAccountTitle => '删除账户';

  @override
  String get profileDeactivateAccountSubText => '删除您的账户';

  @override
  String get profileDeactivateAccountConfirm => '您确定要删除您的账户吗？此操作无法撤销。';

  @override
  String get profileDeactivateAccountButton => '删除';

  @override
  String get profileDeactivateAccountSuccess => '账户删除成功';

  @override
  String get profileDeactivateAccountError => '删除账户失败';

  @override
  String get profileDeactivateAccountUserError => '无法加载用户信息。请重试。';

  @override
  String get profileDeactivateAccountLoading => '正在删除账户...';

  @override
  String get bottomBarStartingFrom => '起价';

  @override
  String get bottomBarPerMonth => '月';

  @override
  String get bottomBarPerDay => '天';

  @override
  String get bottomBarContactUs => '联系我们';

  @override
  String get bottomBarRoomUnavailable => '房间不可预订';

  @override
  String get bottomBarContactCustomerService => '联系客户服务以获取更多信息';

  @override
  String get bottomBarSubtotal => '小计';

  @override
  String get imageViewerClose => '关闭';

  @override
  String imageViewerImageCounter(int current, int total) {
    return '图片 $current/$total';
  }

  @override
  String get detailPropertyPageTitle => '房源详情';

  @override
  String get roomDetailsPageTitle => '房间详情';

  @override
  String get googleSignInNewAccountTitle => '新账户已创建';

  @override
  String get googleSignInNewAccountMessage =>
      '您的账户尚未注册。\n\n注册成功。请检查您的邮箱以验证您的账户。';

  @override
  String get googleSignInWelcomeBackTitle => '欢迎回来';

  @override
  String get googleSignInWelcomeBackMessage => '您已成功使用Google登录！';

  @override
  String get pingSlowConnectionTitle => '网络连接缓慢';

  @override
  String get pingSlowConnectionMessage => '您的互联网连接较慢。这可能影响您的体验。';

  @override
  String get pingNoConnectionTitle => '无互联网连接';

  @override
  String get pingNoConnectionMessage => '无法连接到互联网。请检查您的连接。';

  @override
  String get pingDialogOkButton => '确定';

  @override
  String get chatRoomTitle => '聊天';

  @override
  String get chatInputHint => '输入消息...';

  @override
  String get chatSendButton => '发送';

  @override
  String get chatSelectImage => '选择图片';

  @override
  String get chatImagePreview => '图片预览';

  @override
  String get chatMessageEdited => '已编辑';

  @override
  String get chatNoMessages => '暂无消息';

  @override
  String get chatStartConversation => '开始对话';

  @override
  String get chatLoadingMessages => '正在加载消息...';

  @override
  String get chatErrorLoadingMessages => '加载消息失败';

  @override
  String get chatCreatingConversation => '正在创建对话...';

  @override
  String get chatConversationCreated => '对话创建成功';

  @override
  String get chatMessageSent => '消息已发送';

  @override
  String get chatMessageFailed => '发送消息失败';

  @override
  String get chatImageTooLarge => '图片大小必须小于10MB';

  @override
  String get chatInvalidBooking => '您必须有活动预订才能开始对话';

  @override
  String get chatDuplicateConversation => '对话已存在';

  @override
  String get chatWithFrontOffice => '与前台聊天';

  @override
  String get chatWithHeadOffice => '与总部聊天';

  @override
  String get chatPickFromGallery => '相册';

  @override
  String get chatPickFromCamera => '相机';

  @override
  String get chatCancel => '取消';

  @override
  String get chatSending => '发送中...';

  @override
  String get chatRetry => '重试';

  @override
  String get chatLoadMore => '加载更多消息';

  @override
  String get chatMarkAsRead => '标记为已读';

  @override
  String get chatEditMessage => '编辑消息';

  @override
  String get chatDeleteMessage => '删除消息';

  @override
  String get chatCopyMessage => '复制消息';

  @override
  String get chatImageUploadError => '上传图片失败';

  @override
  String get chatNetworkError => '网络错误。请检查您的连接。';

  @override
  String get chatServerError => '服务器错误。请稍后重试。';

  @override
  String get chatUnknownError => '发生未知错误';

  @override
  String get chatImageFormatError => '仅允许JPG和PNG图片';

  @override
  String get chatImagePickError => '选择图片失败。请重试。';

  @override
  String get showAll => '显示全部';

  @override
  String get loadingPropertyName => '正在加载房源名称';

  @override
  String get loadingAddress => '正在加载地址';

  @override
  String get loadingDistance => '正在加载距离';

  @override
  String get loadingType => '正在加载';

  @override
  String get loadingBestSellerProperty => '正在加载热门房源';

  @override
  String get loadingBudgetProperty => '正在加载预算房源';

  @override
  String get loadingRoomName => '正在加载房间名称';

  @override
  String get loadingDescription => '正在加载描述';

  @override
  String get loadingStatus => '正在加载状态';

  @override
  String get loadingRoomType => '房间类型：正在加载';

  @override
  String get loadingPrice => '价格：Rp 0 / 晚';

  @override
  String get loadingPropertyType => '正在加载房源类型';

  @override
  String get loading => '加载中';

  @override
  String get helpCenter => '帮助中心';

  @override
  String get faqTabLabel => '常见问题';

  @override
  String get contactUsTabLabel => '联系我们';

  @override
  String get noActiveBookings => '暂无订单';

  @override
  String get noActiveBookingsMessage => '您还没有任何订单。开始探索我们的房源，找到您的理想住所！';

  @override
  String get browseProperties => '浏览房源';

  @override
  String get selectAppleAccount => '选择Apple账户';

  @override
  String get selectAccountForLogin => '选择您想用于登录的账户：';

  @override
  String get verified => '已验证';

  @override
  String get notVerified => '未验证';

  @override
  String get useAnotherAppleAccount => '使用其他Apple账户';

  @override
  String get switchAccount => '切换账户';

  @override
  String get addAccount => '添加账户';

  @override
  String get addAnotherAccount => '添加其他账户';

  @override
  String get removeButton => '移除';

  @override
  String get removeAccountDialogTitle => '移除账户';

  @override
  String get removeAccountTooltip => '移除账户';

  @override
  String get accountRemovedSuccess => '账户已移除';

  @override
  String get failedToRemoveAccount => '移除账户失败';

  @override
  String get failedToSwitchAccount => '切换账户失败';

  @override
  String get noAccountsFound => '未找到账户';

  @override
  String get signInWithAppleToAddAccount => '使用Apple登录以添加账户';

  @override
  String get activeStatus => '活跃';

  @override
  String get vaGenerationFailed => '虚拟账户生成失败';

  @override
  String get vaGenerationError => '预订已成功保存，但创建虚拟账户失败。';

  @override
  String get qrisGenerationFailed => 'QRIS生成失败';

  @override
  String get qrisGenerationError => '预订已成功保存，但创建QRIS失败。';

  @override
  String get ccPaymentFailed => '信用卡支付失败';

  @override
  String get ccPaymentError => '预订已成功保存，但处理信用卡支付失败。';

  @override
  String get errorDetail => '错误详情：';

  @override
  String get retryFromBookingDetail => '您可以从预订详情页面重新尝试创建虚拟账户/QRIS。';

  @override
  String get goToMyBooking => '前往我的订单';

  @override
  String get indonesianLanguage => '印尼语';

  @override
  String get englishLanguage => '英语';

  @override
  String get registrationDisabled => '注册不可用';

  @override
  String get registrationDisabledMessage => '注册功能暂时禁用。请稍后重试。';

  @override
  String get notLoggedIn => '未登录';

  @override
  String get edited => '已编辑';

  @override
  String get greetingMorning => '早上好';

  @override
  String get greetingAfternoon => '下午好';

  @override
  String get greetingEvening => '晚上好';

  @override
  String get greetingNight => '晚安';

  @override
  String get homeSubtitle => '今天您想住在哪里？';

  @override
  String get homeBannerTitle => '找到您的梦想家园';

  @override
  String get homeBannerPart1 => '找到您的';

  @override
  String get homeBannerPart2 => '梦想';

  @override
  String get homeBannerPart3 => '家园';

  @override
  String get genderMale => '男性';

  @override
  String get genderFemale => '女性';

  @override
  String get genderMixed => '混合';

  @override
  String get scanQRWithEwallet => '使用您的电子钱包应用扫描此二维码';

  @override
  String get downloadQR => '下载二维码';

  @override
  String get downloadQRSuccess => '二维码已保存至相册';

  @override
  String get downloadQRFailed => '保存二维码失败';

  @override
  String get downloadQRPermissionDenied => '没有保存图片的权限';

  @override
  String get creditCardPayment => '信用卡支付';

  @override
  String get continuePayment => '继续支付';

  @override
  String get ccPaymentNote => '点击上方按钮完成您的信用卡支付';

  @override
  String get expiresIn => '有效期';

  @override
  String get darkModeLabel => '深色模式';

  @override
  String get lightModeLabel => '浅色模式';

  @override
  String get switchToLightMode => '切换到浅色模式';

  @override
  String get switchToDarkMode => '切换到深色模式';
}
