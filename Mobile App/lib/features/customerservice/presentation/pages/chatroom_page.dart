import 'dart:async';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:image_picker/image_picker.dart';
import 'package:skeletonizer/skeletonizer.dart';
import '../../../../core/constants/appcolor_constants.dart';
import '../../../../core/widgets/appbar.dart';
import '../../../../l10n/app_localizations.dart';
import '../../../../core/utils/app_logger.dart';
import '../../../../features/auth/login/provider/auth_provider.dart';
import '../../provider/chat_provider.dart';
import '../widgets/message_bubble.dart';
import '../widgets/message_input_field.dart';
import '../widgets/image_preview_widget.dart';
import '../../../../core/widgets/dialog/imagesourcedialog.dart';
import '../../../../core/widgets/dialog/notificationdialog.dart';
import '../../../../core/services/local_notification_service.dart';
import 'package:path/path.dart' as path;

/// Chat room page - WhatsApp-like UI
class ChatRoomPage extends ConsumerStatefulWidget {
  final int conversationId;
  final String? orderId;
  final String? recipientType;
  final String? roomName;

  const ChatRoomPage({
    super.key,
    required this.conversationId,
    this.orderId,
    this.recipientType,
    this.roomName,
  });

  @override
  ConsumerState<ChatRoomPage> createState() => _ChatRoomPageState();
}

class _ChatRoomPageState extends ConsumerState<ChatRoomPage>
    with WidgetsBindingObserver {
  final TextEditingController _messageController = TextEditingController();
  final TextEditingController _captionController = TextEditingController();
  final ScrollController _scrollController = ScrollController();

  Timer? _pollingTimer;
  File? _selectedImage;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);

    // Set selected conversation
    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref.read(selectedConversationProvider.notifier).select(
          widget.conversationId);

      // Cancel notification for this conversation
      _cancelNotification();

      // Mark as read when page opens
      _markAsRead();

      // Start polling for new messages
      _startPolling();
    });

    // Listen to scroll for loading more messages
    _scrollController.addListener(_onScroll);
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    _messageController.dispose();
    _captionController.dispose();
    _scrollController.dispose();
    _pollingTimer?.cancel();
    super.dispose();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    // Stop polling when app is paused
    if (state == AppLifecycleState.paused) {
      _pollingTimer?.cancel();
    }
    // Resume polling when app is resumed
    else if (state == AppLifecycleState.resumed) {
      _startPolling();
    }
  }

  void _startPolling() {
    _pollingTimer?.cancel();
    _pollingTimer = Timer.periodic(const Duration(seconds: 5), (timer) {
      _refreshConversation();
    });
  }

  void _onScroll() {
    // Disable automatic pagination for now to prevent chat from disappearing
    // Only keep polling refresh working
  }

  Future<void> _refreshConversation() async {
    final user = ref.read(authProvider).user.value;
    if (user != null) {
      // Always use page 1 to get latest messages
      final params = ConversationDetailParams(
        conversationId: widget.conversationId,
        userId: user.id,
        page: 1,
      );
      ref.invalidate(conversationDetailProvider(params));

      // Wait a bit for the provider to refresh
      await Future.delayed(const Duration(milliseconds: 500));
    }
  }

  void _cancelNotification() {
    // Cancel notification for this conversation when page opens
    LocalNotificationService().cancelNotification(widget.conversationId);
    AppLogger.d('Cancelled notification for conversation ${widget.conversationId}', 'CHATROOM-PAGE');
  }

  void _markAsRead() {
    final user = ref.read(authProvider).user.value;
    if (user != null) {
      ref.read(chatControllerProvider.notifier).markAsRead(
            conversationId: widget.conversationId,
            userId: user.id,
          );
    }
  }

  void _updateLastSeenMessage(int messageId) {
    // Update last seen message for background notification tracking
    ref.read(chatControllerProvider.notifier).updateLastSeenMessage(
          conversationId: widget.conversationId,
          messageId: messageId,
        );
  }

  Future<void> _sendTextMessage() async {
    final messageText = _messageController.text.trim();
    if (messageText.isEmpty) return;

    final user = ref.read(authProvider).user.value;
    if (user == null) return;

    final controller = ref.read(chatControllerProvider.notifier);

    // Clear input immediately for better UX
    _messageController.clear();

    final success = await controller.sendTextMessage(
      conversationId: widget.conversationId,
      userId: user.id,
      messageText: messageText,
    );

    if (success) {
      // Scroll to bottom
      _scrollToBottom();
    } else {
      // Restore message if failed
      _messageController.text = messageText;
    }
  }

  Future<void> _sendImageMessage() async {
    if (_selectedImage == null) return;

    final user = ref.read(authProvider).user.value;
    if (user == null) return;

    final controller = ref.read(chatControllerProvider.notifier);
    final caption = _captionController.text.trim();

    final success = await controller.sendImageMessage(
      conversationId: widget.conversationId,
      userId: user.id,
      image: _selectedImage!,
      messageText: caption.isNotEmpty ? caption : null,
    );

    if (success) {
      // Clear image and caption
      setState(() {
        _selectedImage = null;
      });
      _captionController.clear();

      // Scroll to bottom
      _scrollToBottom();
    }
  }

  Future<void> _showImageSourceDialog() async {
    final localizations = AppLocalizations.of(context)!;

    final result = await showImageSourceDialog(
      context,
      title: localizations.chatSelectImage,
      cameraButtonText: localizations.chatPickFromCamera,
      galleryButtonText: localizations.chatPickFromGallery,
      cancelButtonText: localizations.chatCancel,
    );

    if (result == null) return;

    if (result == ImageSourceOption.camera) {
      await _pickImageFromCamera();
    } else if (result == ImageSourceOption.gallery) {
      await _pickImageFromGallery();
    }
  }

  Future<void> _pickImageFromGallery() async {
    final localizations = AppLocalizations.of(context)!;
    final picker = ImagePicker();

    try {
      final pickedFile = await picker.pickImage(source: ImageSource.gallery);

      if (pickedFile != null) {
        // Validate file extension (only JPG and PNG allowed)
        final fileExtension = path.extension(pickedFile.path).toLowerCase();
        if (fileExtension != '.jpg' && fileExtension != '.jpeg' && fileExtension != '.png') {
          if (mounted) {
            showNotificationDialog(
              context,
              localizations.chatImageFormatError,
              defaultIcon: Icons.error_outline,
              iconColor: Colors.red,
            );
          }
          AppLogger.w('Invalid image format: $fileExtension (chat)', 'CHAT-ROOM');
          return;
        }

        setState(() {
          _selectedImage = File(pickedFile.path);
        });
        AppLogger.s('Image selected from gallery: ${pickedFile.path} (chat)', 'CHAT-ROOM');
      } else {
        AppLogger.i('No image selected from gallery (chat)', 'CHAT-ROOM');
      }
    } catch (e, stackTrace) {
      AppLogger.e('Error picking image from gallery', e, stackTrace, 'CHAT-ROOM');
      if (mounted) {
        showNotificationDialog(
          context,
          localizations.chatImagePickError,
          defaultIcon: Icons.error_outline,
          iconColor: Colors.red,
        );
      }
    }
  }

  Future<void> _pickImageFromCamera() async {
    final localizations = AppLocalizations.of(context)!;
    final picker = ImagePicker();

    try {
      final pickedFile = await picker.pickImage(source: ImageSource.camera);

      if (pickedFile != null) {
        // Validate file extension (only JPG and PNG allowed)
        final fileExtension = path.extension(pickedFile.path).toLowerCase();
        if (fileExtension != '.jpg' && fileExtension != '.jpeg' && fileExtension != '.png') {
          if (mounted) {
            showNotificationDialog(
              context,
              localizations.chatImageFormatError,
              defaultIcon: Icons.error_outline,
              iconColor: Colors.red,
            );
          }
          AppLogger.w('Invalid image format: $fileExtension (chat)', 'CHAT-ROOM');
          return;
        }

        setState(() {
          _selectedImage = File(pickedFile.path);
        });
        AppLogger.s('Image taken from camera: ${pickedFile.path} (chat)', 'CHAT-ROOM');
      } else {
        AppLogger.i('No image taken from camera (chat)', 'CHAT-ROOM');
      }
    } catch (e, stackTrace) {
      AppLogger.e('Error taking image from camera', e, stackTrace, 'CHAT-ROOM');
      if (mounted) {
        showNotificationDialog(
          context,
          localizations.chatImagePickError,
          defaultIcon: Icons.error_outline,
          iconColor: Colors.red,
        );
      }
    }
  }

  void _scrollToBottom() {
    if (_scrollController.hasClients) {
      Future.delayed(const Duration(milliseconds: 100), () {
        _scrollController.animateTo(
          0,
          duration: const Duration(milliseconds: 300),
          curve: Curves.easeOut,
        );
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;
    final user = ref.watch(authProvider).user.value;
    final chatState = ref.watch(chatControllerProvider);

    if (user == null) {
      return Scaffold(
        body: const Center(child: Text('Not logged in')),
      );
    }

    final params = ConversationDetailParams(
      conversationId: widget.conversationId,
      userId: user.id,
      page: 1,
    );
    final conversationAsync = ref.watch(conversationDetailProvider(params));

    return Scaffold(
      appBar: PreferredSize(
        preferredSize: const Size.fromHeight(kToolbarHeight),
        child: CustomAppBar(
          title: widget.recipientType != null
              ? (widget.recipientType == 'fo'
                  ? localizations.chatWithFrontOffice
                  : localizations.chatWithHeadOffice)
              : localizations.chatRoomTitle,
          subtitle: widget.roomName != null && widget.roomName!.isNotEmpty
              ? widget.roomName
              : null,
          showBackButton: true,
        ),
      ),
      body: Column(
        children: [
          // Messages list
          Expanded(
            child: conversationAsync.when(
              data: (conversationDetail) {
                final messages = conversationDetail.messages;

                if (messages.isEmpty) {
                  return RefreshIndicator(
                    onRefresh: _refreshConversation,
                    child: SingleChildScrollView(
                      physics: const AlwaysScrollableScrollPhysics(),
                      child: SizedBox(
                        height: MediaQuery.of(context).size.height * 0.7,
                        child: _buildEmptyState(localizations),
                      ),
                    ),
                  );
                }

                // Update last seen message (latest message is at index 0 since reversed)
                WidgetsBinding.instance.addPostFrameCallback((_) {
                  if (messages.isNotEmpty) {
                    _updateLastSeenMessage(messages.first.id);
                  }
                });

                return RefreshIndicator(
                  onRefresh: _refreshConversation,
                  child: ListView.builder(
                    controller: _scrollController,
                    reverse: true, // Show newest at bottom
                    physics: const AlwaysScrollableScrollPhysics(),
                    padding: const EdgeInsets.symmetric(
                      horizontal: 8,
                      vertical: 16,
                    ),
                    itemCount: messages.length,
                    itemBuilder: (context, index) {
                      final message = messages[index];
                      final isFromCurrentUser = message.isFromUser(user.id);

                      return MessageBubble(
                        message: message,
                        isFromCurrentUser: isFromCurrentUser,
                        onLongPress: isFromCurrentUser
                            ? () => _showMessageOptions(message)
                            : null,
                      );
                    },
                  ),
                );
              },
              loading: () => Skeletonizer(
                enabled: true,
                child: ListView.builder(
                  reverse: true,
                  padding: const EdgeInsets.symmetric(
                    horizontal: 8,
                    vertical: 16,
                  ),
                  itemCount: 5,
                  itemBuilder: (context, index) {
                    final isRight = index % 2 == 0;
                    return Align(
                      alignment: isRight ? Alignment.centerRight : Alignment.centerLeft,
                      child: Container(
                        margin: EdgeInsets.only(
                          left: isRight ? 64 : 8,
                          right: isRight ? 8 : 64,
                          top: 4,
                          bottom: 4,
                        ),
                        child: Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 12,
                            vertical: 8,
                          ),
                          decoration: BoxDecoration(
                            // Use adaptive primary color for left-side message bubbles
                            color: isRight
                                ? AppColors.secondaryColor
                                : AppColors.primaryAdaptive(context),
                            borderRadius: BorderRadius.circular(16),
                          ),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                'Loading message text placeholder',
                                style: const TextStyle(
                                  fontSize: 14,
                                  color: Colors.white,
                                ),
                              ),
                              const SizedBox(height: 4),
                              Text(
                                '00:00',
                                style: TextStyle(
                                  fontSize: 10,
                                  color: Colors.white.withValues(alpha: 0.7),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    );
                  },
                ),
              ),
              error: (error, stack) {
                AppLogger.e('Error loading conversation', error, stack, 'CHAT-ROOM');
                return RefreshIndicator(
                  onRefresh: _refreshConversation,
                  child: SingleChildScrollView(
                    physics: const AlwaysScrollableScrollPhysics(),
                    child: SizedBox(
                      height: MediaQuery.of(context).size.height * 0.7,
                      child: _buildErrorState(localizations, error),
                    ),
                  ),
                );
              },
            ),
          ),

          // Image preview (if selected)
          if (_selectedImage != null)
            ImagePreviewWidget(
              image: _selectedImage!,
              captionController: _captionController,
              onRemove: () {
                setState(() {
                  _selectedImage = null;
                });
                _captionController.clear();
              },
            ),

          // Error message
          if (chatState.error != null)
            Container(
              padding: const EdgeInsets.all(12),
              color: Colors.red.shade50,
              child: Row(
                children: [
                  Icon(Icons.error_outline, color: Colors.red.shade700, size: 20),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      chatState.error!,
                      style: TextStyle(
                        fontSize: 12,
                        color: Colors.red.shade700,
                      ),
                    ),
                  ),
                  IconButton(
                    onPressed: () {
                      ref.read(chatControllerProvider.notifier).clearError();
                    },
                    icon: const Icon(Icons.close, size: 16),
                    color: Colors.red.shade700,
                  ),
                ],
              ),
            ),

          // Message input
          if (_selectedImage != null)
            // Send image button
            Container(
              padding: const EdgeInsets.all(16),
              color: Colors.white,
              child: ElevatedButton(
                onPressed: chatState.isUploadingImage ? null : _sendImageMessage,
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppColors.secondaryColor,
                  foregroundColor: Colors.white,
                  minimumSize: const Size(double.infinity, 48),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12),
                  ),
                ),
                child: chatState.isUploadingImage
                    ? const SizedBox(
                        width: 20,
                        height: 20,
                        child: CircularProgressIndicator(
                          strokeWidth: 2,
                          color: Colors.white,
                        ),
                      )
                    : const Text(
                        'Send Image',
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
              ),
            )
          else
            // Text input
            MessageInputField(
              controller: _messageController,
              onSend: _sendTextMessage,
              onImagePicker: _showImageSourceDialog,
              isLoading: chatState.isSendingMessage,
            ),
        ],
      ),
    );
  }

  Widget _buildEmptyState(AppLocalizations localizations) {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(
            Icons.chat_bubble_outline,
            size: 80,
            color: Colors.grey.shade300,
          ),
          const SizedBox(height: 16),
          Text(
            localizations.chatNoMessages,
            style: TextStyle(
              fontSize: 16,
              color: Colors.grey.shade600,
              fontWeight: FontWeight.w500,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            localizations.chatStartConversation,
            style: TextStyle(
              fontSize: 14,
              color: Colors.grey.shade500,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildErrorState(AppLocalizations localizations, Object error) {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(
            Icons.error_outline,
            size: 80,
            color: Colors.red.shade300,
          ),
          const SizedBox(height: 16),
          Text(
            localizations.chatErrorLoadingMessages,
            style: const TextStyle(
              fontSize: 16,
              color: Colors.black87,
              fontWeight: FontWeight.w500,
            ),
          ),
          const SizedBox(height: 8),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 32),
            child: Text(
              error.toString(),
              textAlign: TextAlign.center,
              style: TextStyle(
                fontSize: 14,
                color: Colors.grey.shade600,
              ),
            ),
          ),
          const SizedBox(height: 24),
          ElevatedButton.icon(
            onPressed: _refreshConversation,
            icon: const Icon(Icons.refresh),
            label: Text(localizations.tryAgain),
            style: ElevatedButton.styleFrom(
              // Use adaptive primary color for retry button background
              backgroundColor: AppColors.primaryAdaptive(context),
              foregroundColor: Colors.white,
              padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12),
              ),
            ),
          ),
        ],
      ),
    );
  }

  void _showMessageOptions(message) {
    // TODO: Implement edit/delete message
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (context) => SafeArea(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            ListTile(
              // Remove const — adaptive color requires context, not a compile-time constant
              leading: Icon(Icons.edit, color: AppColors.primaryAdaptive(context)),
              title: const Text('Edit'),
              onTap: () {
                Navigator.pop(context);
                // TODO: Show edit dialog
              },
            ),
            const SizedBox(height: 8),
          ],
        ),
      ),
    );
  }
}
