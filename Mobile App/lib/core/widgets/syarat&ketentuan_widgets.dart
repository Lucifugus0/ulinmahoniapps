import 'package:flutter/material.dart';
import 'package:ulinmahoniapps/core/constants/appcolor_constants.dart';
import 'package:ulinmahoniapps/core/constants/app_asset_constants.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import 'package:ulinmahoniapps/core/constants/app_text_constants.dart';
import 'package:flutter/gestures.dart';
import 'package:url_launcher/url_launcher.dart';

class TermsAndConditionsDialog extends StatefulWidget {
  final bool isTerms;
  final bool isPrivacy;

  const TermsAndConditionsDialog({
    super.key,
    required this.isTerms,
    required this.isPrivacy,
  }) : assert(
  isTerms != isPrivacy,
  'Hanya salah satu dari isTerms atau isPrivacy yang boleh bernilai true.',
  );

  @override
  _TermsAndConditionsDialogState createState() =>
      _TermsAndConditionsDialogState();
}

class _TermsAndConditionsDialogState extends State<TermsAndConditionsDialog> {
  bool _agreedToTerms = false;
  bool _hasScrolledToBottom = false;
  final ScrollController _scrollController = ScrollController();

  // --- Fungsi dan Konstanta URL Baru ---

  String _getPolicyUrl() {
    return widget.isTerms
        ? 'https://web.ulinmahoni.com/terms-of-services'
        : 'https://web.ulinmahoni.com/privacy-policy';
  }

  // CATATAN PENTING: Untuk menjalankan fungsi ini, Anda harus menambahkan package
  // 'url_launcher' ke pubspec.yaml Anda dan mengimpornya.
  Future<void> _launchUrl(String url) async {
    final Uri uri = Uri.parse(url);
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri);
    } else {
      // Tampilkan pesan error jika gagal membuka URL
      throw Exception('Could not launch $url');
    }
  }

  // --- Akhir Fungsi Baru ---


  String _getTitle(AppLocalizations localizations) {
    return widget.isTerms
        ? localizations.dialogTermsTitle
        : localizations.dialogPrivacyTitle;
  }

  String _getContent(AppLocalizations localizations) {
    return widget.isTerms
        ? kTermsAndConditionsContent
        : kPrivacyPolicyContent;
  }

  String _getAgreeText(AppLocalizations localizations) {
    return widget.isTerms
        ? localizations.dialogTermsAgree
        : localizations.dialogPrivacyAgree;
  }

  // --- Widget Baru untuk Teks Persetujuan dengan Hyperlink ---
  TextSpan _buildAgreementText(
      BuildContext context, AppLocalizations localizations) {
    final isTerms = widget.isTerms;
    final String fullText = _getAgreeText(localizations);
    final String url = _getPolicyUrl();
    final String linkText = isTerms ? localizations.dialogTermsAgree : localizations.dialogPrivacyAgree;
    final int linkStartIndex = fullText.indexOf(linkText);

    if (linkStartIndex == -1) {
      return TextSpan(
        text: fullText,
        style: Theme.of(context).textTheme.bodyMedium,
      );
    }

    // Membagi teks menjadi 3 bagian: Awalan, Link, dan Akhiran
    final String prefix = fullText.substring(0, linkStartIndex);
    final String suffix = fullText.substring(linkStartIndex + linkText.length);

    final TextStyle defaultStyle = Theme.of(context).textTheme.bodyMedium!;
    final TextStyle linkStyle = defaultStyle.copyWith(
      color: Colors.lightBlue, // Biru muda seperti permintaan
      decoration: TextDecoration.underline,
    );

    return TextSpan(
      style: defaultStyle,
      children: <TextSpan>[
        TextSpan(text: prefix),
        TextSpan(
          text: linkText,
          style: linkStyle,
          recognizer: TapGestureRecognizer()
            ..onTap = () {
              if (_hasScrolledToBottom) {
                _launchUrl(url);
              }
            },
        ),
        TextSpan(text: suffix),
      ],
    );
  }
  // --- Akhir Widget Baru ---


  @override
  void initState() {
    super.initState();
    _scrollController.addListener(() {
      if (_scrollController.position.pixels >=
          _scrollController.position.maxScrollExtent - 5) {
        if (!_hasScrolledToBottom) {
          setState(() {
            _hasScrolledToBottom = true;
          });
        }
      } else {
        if (_hasScrolledToBottom) {
          setState(() {
            _hasScrolledToBottom = false;
          });
        }
      }
    });
  }

  @override
  void dispose() {
    _scrollController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Dialog(
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(20),
      ),
      elevation: 0,
      backgroundColor: Colors.transparent,
      child: contentBox(context),
    );
  }

  Widget contentBox(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;
    final screenHeight = MediaQuery.of(context).size.height;
    final dialogMaxHeight = screenHeight * 0.8;
    // Dark mode detection for dialog background and inner container colors
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Container(
      padding: const EdgeInsets.all(20),
      constraints: BoxConstraints(maxHeight: dialogMaxHeight),
      decoration: BoxDecoration(
        shape: BoxShape.rectangle,
        // Use dark surface color in dark mode, white in light mode
        color: isDark ? AppColors.surfaceDark : Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.3),
            spreadRadius: 2,
            blurRadius: 7,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: <Widget>[
          Align(
            alignment: Alignment.center,
            child: Padding(
              padding: const EdgeInsets.only(bottom: 15.0),
              child: Image.asset(
                AppImage.logo,
                height: 60,
                errorBuilder: (context, error, stackTrace) {
                  // Use primaryAdaptive for the fallback icon color
                  return Icon(
                    Icons.apartment,
                    size: 60,
                    color: AppColors.primaryAdaptive(context),
                  );
                },
              ),
            ),
          ),
          Text(
            _getTitle(localizations),
            style: Theme.of(context).textTheme.titleLarge?.copyWith(
              fontWeight: FontWeight.bold,
              // Use primaryAdaptive for the dialog title color
              color: AppColors.primaryAdaptive(context),
            ),
          ),
          const SizedBox(height: 15),


          Expanded(
            child: Scrollbar(
              controller: _scrollController,
              thumbVisibility: true,
              interactive: true,
              radius: const Radius.circular(10),
              thickness: 8,
              child: SingleChildScrollView(
                controller: _scrollController,
                child: Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    // Use elevated dark surface in dark mode, light grey in light mode
                    color: isDark ? AppColors.surfaceDarkElevated : Colors.grey[50],
                    borderRadius: BorderRadius.circular(15),
                    border: Border.all(
                      // Use primaryAdaptive for the scroll container border
                      color: AppColors.primaryAdaptive(context).withValues(alpha: 0.2),
                      width: 1,
                    ),
                  ),
                  child: Text(
                    _getContent(localizations),
                    style: Theme.of(context)
                        .textTheme
                        .bodyMedium
                        ?.copyWith(height: 1.5),
                  ),
                ),
              ),
            ),
          ),

          const SizedBox(height: 20),
          Row(
            children: [
              Checkbox(
                value: _agreedToTerms,
                onChanged: _hasScrolledToBottom
                    ? (bool? value) {
                  setState(() {
                    _agreedToTerms = value ?? false;
                  });
                }
                    : null,
                // Use primaryAdaptive for the checkbox active color
                activeColor: AppColors.primaryAdaptive(context),
                checkColor: Colors.white,
                // Add visible border for unchecked state in dark mode
                side: isDark ? const BorderSide(color: Colors.white70, width: 2) : null,
              ),
              Expanded(
                // Mengganti GestureDetector untuk hanya mengaktifkan RichText
                child: GestureDetector(
                  // Tap pada area teks (selain hyperlink) akan tetap berfungsi untuk toggle checkbox
                  onTap: _hasScrolledToBottom
                      ? () {
                    setState(() {
                      _agreedToTerms = !_agreedToTerms;
                    });
                  }
                      : null,
                  child: RichText(
                    text: _buildAgreementText(context, localizations),
                  ),
                ),
              ),
            ],
          ),

          const SizedBox(height: 20),
          SizedBox(
            width: double.infinity,
            child: ElevatedButton(
              onPressed: _agreedToTerms
                  ? () {
                Navigator.of(context).pop(true);
              }
                  : null,
              style: ElevatedButton.styleFrom(
                // Use primaryAdaptive for the continue button background
                backgroundColor: AppColors.primaryAdaptive(context),
                // Use dark-aware disabled background color
                disabledBackgroundColor: isDark ? Colors.grey[700] : Colors.grey[300],
                padding: const EdgeInsets.symmetric(vertical: 15),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(10),
                ),
                elevation: 3,
              ),
              child: Text(
                localizations.dialogTermsContinueButton,
                style: Theme.of(context).textTheme.titleMedium?.copyWith(
                  color: Colors.white,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}