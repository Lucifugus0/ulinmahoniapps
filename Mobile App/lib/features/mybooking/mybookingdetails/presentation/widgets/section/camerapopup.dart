import 'package:flutter/material.dart';
import 'package:camera/camera.dart';
import 'package:image_picker/image_picker.dart';
import 'dart:io';
import 'package:ulinmahoniapps/main.dart';
import 'package:ulinmahoniapps/core/constants/appcolor_constants.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import 'package:ulinmahoniapps/core/widgets/dialog/notificationdialog.dart';
import 'package:ulinmahoniapps/core/utils/app_logger.dart';

class CameraPopupWidget extends StatefulWidget {
  final String idrec;
  const CameraPopupWidget({
    Key? key,
    required this.idrec,
  }) : super(key: key);

  @override
  State<CameraPopupWidget> createState() => _CameraPopupWidgetState();
}

class _CameraPopupWidgetState extends State<CameraPopupWidget> {
  late CameraController _controller;
  late Future<void> _initializeControllerFuture;
  File? _capturedImage;
  bool _isInitializing = true;
  String? _cameraError;

  @override
  void initState() {
    super.initState();
    _initializeCamera();
  }

  Future<void> _initializeCamera() async {
    final localizations = AppLocalizations.of(context);
    setState(() {
      _isInitializing = true;
      _cameraError = null;
    });
    try {
      // CRITICAL FIX: Check if cameras list is empty AND if widget is still mounted
      if (cameras.isEmpty) {
        if (mounted) {
          setState(() {
            _cameraError = localizations?.cameraNotFoundError ?? "Tidak ada kamera yang ditemukan.";
            _isInitializing = false;
          });
        }
        return;
      }
      _controller = CameraController(
        cameras[0],
        ResolutionPreset.medium,
        imageFormatGroup: ImageFormatGroup.jpeg,
      );

      _initializeControllerFuture = _controller.initialize();
      await _initializeControllerFuture;
      AppLogger.s('Camera initialized successfully (${widget.idrec})', 'CAMERA');
    } on CameraException catch (e) {
      AppLogger.w('Error initializing camera (${widget.idrec}): $e', 'CAMERA');
      if (mounted) {
        setState(() {
          _cameraError = "${localizations?.cameraInitFailed ?? "Gagal menginisialisasi kamera"}: ${e.description}";
        });
      }
    } catch (e) {
      AppLogger.w('Unexpected error initializing camera (${widget.idrec}): $e', 'CAMERA');
      if (mounted) {
        setState(() {
          _cameraError = "${localizations?.cameraUnexpectedError ?? "Terjadi kesalahan tak terduga"}: $e";
        });
      }
    } finally {
      if (mounted) {
        setState(() {
          _isInitializing = false;
        });
      }
    }
  }

  @override
  void dispose() {
    // CRITICAL FIX: Check if controller is initialized before disposing
    if (_controller.value.isInitialized) {
      _controller.dispose();
    }
    super.dispose();
  }

  Future<void> _takePicture() async {
    final localizations = AppLocalizations.of(context)!;
    if (!_controller.value.isInitialized) {
      showNotificationDialog(
        context,
        localizations.cameraSelectFirstError,
        defaultIcon: Icons.info_outline,
      );
      return;
    }
    if (_controller.value.isTakingPicture) {
      return;
    }

    try {
      AppLogger.d('Taking picture (${widget.idrec})', 'CAMERA');
      final XFile image = await _controller.takePicture();
      setState(() {
        _capturedImage = File(image.path);
      });
      AppLogger.s('Picture taken: ${image.path} (${widget.idrec})', 'CAMERA');
    } on CameraException catch (e) {
      AppLogger.w('Error taking picture (${widget.idrec}): $e', 'CAMERA');
      showNotificationDialog(
        context,
        '${localizations.cameraCaptureFailed}: ${e.description}',
        defaultIcon: Icons.error_outline,
        iconColor: Colors.red,
      );
    }
  }

  Future<void> _pickImageFromGallery() async {
    final localizations = AppLocalizations.of(context)!;
    final picker = ImagePicker();
    final pickedFile = await picker.pickImage(source: ImageSource.gallery);

    if (pickedFile != null) {
      AppLogger.s('Image selected from gallery: ${pickedFile.path} (${widget.idrec})', 'CAMERA');
      Navigator.of(context).pop(File(pickedFile.path));
    } else {
      AppLogger.i('No image selected from gallery (${widget.idrec})', 'CAMERA');
    }
  }

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!; 
    return Container(
      width: double.maxFinite,
      height: MediaQuery.of(context).size.height * 0.8,
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          AppBar(
            backgroundColor: Colors.white,
            elevation: 0,
            leading: IconButton(
              icon: const Icon(Icons.close, color: Colors.black),
              onPressed: () {
                Navigator.of(context).pop();
                AppLogger.i('Camera popup closed without selecting image (${widget.idrec})', 'CAMERA');
              },
            ),
            title: Text(
              localizations.cameraPopupTitle, 
              style: const TextStyle(color: Colors.black, fontSize: 15),
            ),
            actions: [
              if (_capturedImage == null)
                IconButton(
                  // Remove const: color is adaptive (runtime context)
                  icon: Icon(Icons.photo_library, color: AppColors.primaryAdaptive(context)),
                  onPressed: _pickImageFromGallery,
                  tooltip: localizations.cameraGalleryTooltip, 
                ),
            ],
            shape: const RoundedRectangleBorder(
              borderRadius: BorderRadius.only(
                topLeft: Radius.circular(20),
                topRight: Radius.circular(20),
                bottomLeft: Radius.circular(20),
                bottomRight: Radius.circular(20),
              ),
            ),
          ),
          Expanded(
            child: _isInitializing
                ? const Center(child: CircularProgressIndicator())
                : _cameraError != null
                ? Center(
              child: Padding(
                padding: const EdgeInsets.all(16.0),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Icon(Icons.error_outline, color: AppColors.secondaryColor, size: 60),
                    const SizedBox(height: 10),
                    Text(
                      _cameraError!,
                      textAlign: TextAlign.center,
                      style: const TextStyle(color: AppColors.secondaryColor, fontSize: 16),
                    ),
                    const SizedBox(height: 20),
                    ElevatedButton(
                      onPressed: _initializeCamera,
                      child: Text(localizations.cameraTryAgain), 
                    ),
                    const SizedBox(height: 10),
                    ElevatedButton(
                      onPressed: _pickImageFromGallery,
                      child: Text(localizations.cameraGalleryButton), 
                    ),
                  ],
                ),
              ),
            )
                : _capturedImage != null
                ? _buildImageViewer()
                : _buildCameraPreview(),
          ),
        ],
      ),
    );
  }

  Widget _buildCameraPreview() {
    return FutureBuilder<void>(
      future: _initializeControllerFuture,
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.done) {
          return Stack(
            alignment:Alignment.center,
            children: [
              Positioned.fill(
                child: AspectRatio(
                  aspectRatio: _controller.value.aspectRatio,
                  child: CameraPreview(_controller),
                ),
              ),
              Positioned(
                bottom: 20,
                child: FloatingActionButton(
                  // Use primaryAdaptive for dark/light mode compatibility
                  backgroundColor: AppColors.primaryAdaptive(context),
                  onPressed: _takePicture,
                  child: const Icon(Icons.camera),
                ),
              ),
            ],
          );
        } else {
          return const Center(child: CircularProgressIndicator());
        }
      },
    );
  }

  Widget _buildImageViewer() {
    final localizations = AppLocalizations.of(context)!;
    return Column(
      children: [
        Expanded(
          child: Padding(
            padding: const EdgeInsets.all(8.0),
            child: Image.file(
              _capturedImage!,
              fit: BoxFit.contain,
            ),
          ),
        ),
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 20.0, vertical: 10),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceEvenly,
            children: [
              Expanded(
                child: ElevatedButton.icon(
                  onPressed: () {
                    setState(() {
                      _capturedImage = null;
                      AppLogger.d('Retaking picture (${widget.idrec})', 'CAMERA');
                    });
                  },
                  icon: const Icon(Icons.refresh),
                  label: Text(localizations.cameraRetake), 
                  style: ElevatedButton.styleFrom(
                    // Use primaryAdaptive for dark/light mode compatibility
                  backgroundColor: AppColors.primaryAdaptive(context),
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 12),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                  ),
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 10.0),
                  child: ElevatedButton.icon(
                    onPressed: () {
                      Navigator.of(context).pop(_capturedImage);
                      AppLogger.s('Using captured image (${widget.idrec})', 'CAMERA');
                    },
                    icon: const Icon(Icons.check),
                    label: Text(localizations.cameraUseThisImage), 
                    style: ElevatedButton.styleFrom(
                      // Use primaryAdaptive for dark/light mode compatibility
                  backgroundColor: AppColors.primaryAdaptive(context),
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 12),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }
}