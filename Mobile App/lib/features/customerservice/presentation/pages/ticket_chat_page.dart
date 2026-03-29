import 'dart:async';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:image_picker/image_picker.dart';
import 'package:skeletonizer/skeletonizer.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/appcolor_constants.dart';
import '../../../../core/utils/app_logger.dart';
import '../../../../l10n/app_localizations.dart';
import '../../../../features/auth/login/provider/auth_provider.dart';
import '../../../../core/widgets/dialog/imagesourcedialog.dart';
import '../../../../core/widgets/dialog/notificationdialog.dart';
import '../../model/ticket_message_model.dart';
import '../../data/repositories/ticket_repository.dart';
import '../../provider/ticket_provider.dart';
import '../../controller/ticket_controller.dart';
import '../widgets/image_preview_widget.dart';
import 'package:path/path.dart' as path;

/// TicketChatPage — Chat room for a customer service ticket.
/// Shows messages in a WhatsApp-like bubble layout with support for text,
/// image, and system messages. Includes 5-second polling for new messages.
class TicketChatPage extends ConsumerStatefulWidget {
  /// The ticket ID to display messages for
  final int ticketId;

  const TicketChatPage({
    super.key,
    required this.ticketId,
  });

  @override
  ConsumerState<TicketChatPage> createState() => _TicketChatPageState();
}

class _TicketChatPageState extends ConsumerState<TicketChatPage>
    with WidgetsBindingObserver {
  /// Text controller for message input field
  final TextEditingController _messageController = TextEditingController();

  /// Text controller for image caption input
  final TextEditingController _captionController = TextEditingController();

  /// Scroll controller for the message list (reversed)
  final ScrollController _scrollController = ScrollController();

  /// Timer for polling new messages every 5 seconds
  Timer? _pollingTimer;

  /// Currently selected image for sending
  File? _selectedImage;

  /// Guard flag — prevents double-tap sends before Riverpod state rebuilds
  bool _isSending = false;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);

    /// Initialize ticket state after build — set selected ticket, mark as read, start polling
    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref.read(selectedTicketProvider.notifier).state = widget.ticketId;
      _markAsRead();
      _startPolling();
    });
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

  /// Handle app lifecycle changes — pause/resume polling
  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state == AppLifecycleState.paused) {
      _pollingTimer?.cancel();
    } else if (state == AppLifecycleState.resumed) {
      _startPolling();
    }
  }

  /// Start 5-second polling timer to refresh messages
  void _startPolling() {
    _pollingTimer?.cancel();
    // Poll every 3 seconds for faster message updates
    _pollingTimer = Timer.periodic(const Duration(seconds: 3), (timer) {
      _refreshMessages();
    });
  }

  /// Refresh ticket messages silently using refresh() instead of invalidate()
  /// to avoid full reload/loading state flicker
  Future<void> _refreshMessages() async {
    final user = ref.read(authProvider).user.value;
    if (user != null) {
      final params = TicketDetailParams(
        ticketId: widget.ticketId,
        userId: user.id,
      );
      // refresh() re-fetches without showing loading state (keeps previous data)
      ref.refresh(ticketDetailProvider(params));
    }
  }

  /// Mark ticket as read via the controller
  void _markAsRead() {
    final user = ref.read(authProvider).user.value;
    if (user != null) {
      ref
          .read(ticketControllerProvider.notifier)
          .markAsRead(widget.ticketId, user.id);
    }
  }

  /// Send a text message to the ticket
  Future<void> _sendTextMessage() async {
    final messageText = _messageController.text.trim();
    if (messageText.isEmpty) return;

    final user = ref.read(authProvider).user.value;
    if (user == null) return;

    final controller = ref.read(ticketControllerProvider.notifier);

    /// Clear input immediately for better UX
    _messageController.clear();

    final message = await controller.sendTextMessage(
      widget.ticketId,
      user.id,
      messageText,
    );

    if (message != null) {
      _scrollToBottom();
      _refreshMessages();
    } else {
      /// Restore message text if sending failed
      _messageController.text = messageText;
    }
  }

  /// Send an image message to the ticket with optional caption.
  /// Closes the preview immediately and guards against double-sends.
  Future<void> _sendImageMessage() async {
    /* Guard: ignore tap if no image selected or already sending */
    if (_selectedImage == null || _isSending) return;

    final user = ref.read(authProvider).user.value;
    if (user == null) return;

    /* Lock sending and capture state before clearing */
    final imageToSend = _selectedImage!;
    final caption = _captionController.text.trim();
    setState(() {
      _isSending = true;
      /* Close preview immediately — prevents multiple sends while upload is in-flight */
      _selectedImage = null;
    });
    _captionController.clear();

    final controller = ref.read(ticketControllerProvider.notifier);
    final message = await controller.sendImageMessage(
      widget.ticketId,
      user.id,
      imageToSend,
      caption: caption.isNotEmpty ? caption : null,
    );

    if (!mounted) return;
    setState(() { _isSending = false; });

    if (message != null) {
      _scrollToBottom();
      _refreshMessages();
    }
  }

  /// Show image source selection dialog (camera or gallery)
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
      await _pickImage(ImageSource.camera);
    } else if (result == ImageSourceOption.gallery) {
      await _pickImage(ImageSource.gallery);
    }
  }

  /// Pick an image from the given source (camera or gallery).
  /// Validates the file format (only JPG/PNG allowed).
  Future<void> _pickImage(ImageSource source) async {
    final localizations = AppLocalizations.of(context)!;
    final picker = ImagePicker();

    try {
      final pickedFile = await picker.pickImage(source: source);

      if (pickedFile != null) {
        /// Validate file extension — only JPG and PNG allowed
        final fileExtension = path.extension(pickedFile.path).toLowerCase();
        if (fileExtension != '.jpg' &&
            fileExtension != '.jpeg' &&
            fileExtension != '.png') {
          if (mounted) {
            showNotificationDialog(
              context,
              localizations.chatImageFormatError,
              defaultIcon: Icons.error_outline,
              iconColor: Colors.red,
            );
          }
          AppLogger.w(
              'Invalid image format: $fileExtension', 'TICKET-CHAT');
          return;
        }

        setState(() {
          _selectedImage = File(pickedFile.path);
        });
        AppLogger.s('Image selected: ${pickedFile.path}', 'TICKET-CHAT');
      }
    } catch (e, stackTrace) {
      AppLogger.e('Error picking image', e, stackTrace, 'TICKET-CHAT');
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

  /// Scroll the message list to the bottom (newest messages)
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

  /// Close the ticket and refresh the detail view
  Future<void> _closeTicket() async {
    final user = ref.read(authProvider).user.value;
    if (user == null) return;

    final confirmed = await _showConfirmDialog(
      'Close Ticket',
      'Are you sure you want to close this ticket?',
    );

    if (confirmed != true) return;

    final success = await ref
        .read(ticketControllerProvider.notifier)
        .closeTicket(widget.ticketId, user.id);

    if (success) {
      _refreshMessages();
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Ticket closed')),
        );
      }
    }
  }

  /// Reopen the ticket and refresh the detail view
  Future<void> _reopenTicket() async {
    final user = ref.read(authProvider).user.value;
    if (user == null) return;

    final confirmed = await _showConfirmDialog(
      'Reopen Ticket',
      'Are you sure you want to reopen this ticket?',
    );

    if (confirmed != true) return;

    final success = await ref
        .read(ticketControllerProvider.notifier)
        .reopenTicket(widget.ticketId, user.id);

    if (success) {
      _refreshMessages();
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Ticket reopened')),
        );
      }
    }
  }

  /// Show a confirmation dialog and return the user's choice
  Future<bool?> _showConfirmDialog(String title, String content) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        backgroundColor: isDark ? AppColors.surfaceDark : Colors.white,
        title: Text(
          title,
          style: TextStyle(
            color: isDark ? AppColors.fontColorDark : AppColors.fontColorLight,
          ),
        ),
        content: Text(
          content,
          style: TextStyle(
            color: isDark
                ? AppColors.fontColorDarkMuted
                : AppColors.fontColorLightMuted,
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Cancel'),
          ),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            child: Text(
              'Confirm',
              style: TextStyle(color: AppColors.primaryColor),
            ),
          ),
        ],
      ),
    );
  }

  /// Get color for ticket status badge in the app bar
  Color _getStatusColor(String status) {
    switch (status) {
      case 'open':
        return AppColors.green;
      case 'in_progress':
        return AppColors.blue;
      case 'closed':
        return Colors.grey;
      case 'reopened':
        return AppColors.amber;
      default:
        return Colors.grey;
    }
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final user = ref.watch(authProvider).user.value;
    final ticketState = ref.watch(ticketControllerProvider);

    if (user == null) {
      return const Scaffold(
        body: Center(child: Text('Not logged in')),
      );
    }

    final params = TicketDetailParams(
      ticketId: widget.ticketId,
      userId: user.id,
    );
    final detailAsync = ref.watch(ticketDetailProvider(params));

    return Scaffold(
      backgroundColor:
          isDark ? AppColors.backgroundDark : AppColors.backgroundLight,
      appBar: _buildAppBar(detailAsync, isDark),
      body: Column(
        children: [
          /// Messages list area
          Expanded(
            child: detailAsync.when(
              data: (detail) {
                if (detail == null) {
                  return _buildErrorState('Ticket not found', isDark);
                }
                return _buildMessageList(detail.messages, user.id, isDark);
              },
              loading: () => _buildSkeletonMessages(isDark),
              error: (error, stack) {
                AppLogger.e(
                    'Error loading ticket detail', error, stack, 'TICKET-CHAT');
                return _buildErrorState('Failed to load messages', isDark);
              },
            ),
          ),

          /// Image preview widget (when an image is selected for sending)
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

          /// Error message bar
          if (ticketState.error != null)
            Container(
              padding: const EdgeInsets.all(12),
              color: Colors.red.shade50,
              child: Row(
                children: [
                  Icon(Icons.error_outline,
                      color: Colors.red.shade700, size: 20),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      ticketState.error!,
                      style: TextStyle(
                          fontSize: 12, color: Colors.red.shade700),
                    ),
                  ),
                ],
              ),
            ),

          /// Message input area — only shown when ticket is not closed
          _buildInputArea(detailAsync, ticketState, isDark),
        ],
      ),
    );
  }

  /// Build the app bar with ticket info: number, status badge, subject subtitle,
  /// and action buttons (Close/Reopen).
  PreferredSizeWidget _buildAppBar(
      AsyncValue<TicketDetailResult?> detailAsync, bool isDark) {
    return AppBar(
      backgroundColor: AppColors.primaryColor,
      leading: IconButton(
        icon: const Icon(Icons.arrow_back, color: Colors.white),
        onPressed: () => context.pop(),
      ),
      title: detailAsync.when(
        data: (detail) {
          if (detail == null) {
            return const Text('Ticket',
                style: TextStyle(color: Colors.white));
          }
          final ticket = detail.ticket;

          return Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              /// Ticket number + status badge row
              Row(
                children: [
                  Text(
                    ticket.ticketNumber,
                    style: const TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.w600,
                      color: Colors.white,
                    ),
                  ),
                  const SizedBox(width: 8),

                  /// Status badge
                  Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                    decoration: BoxDecoration(
                      color: _getStatusColor(ticket.ticketStatus)
                          .withValues(alpha: 0.25),
                      borderRadius: BorderRadius.circular(4),
                    ),
                    child: Text(
                      ticket.ticketStatus
                          .replaceAll('_', ' ')
                          .toUpperCase(),
                      style: TextStyle(
                        fontSize: 10,
                        fontWeight: FontWeight.w600,
                        color: Colors.white,
                      ),
                    ),
                  ),
                ],
              ),

              /// Subject subtitle
              Text(
                ticket.subject,
                style: TextStyle(
                  fontSize: 12,
                  color: Colors.white.withValues(alpha: 0.85),
                ),
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
              ),
            ],
          );
        },
        loading: () =>
            const Text('Loading...', style: TextStyle(color: Colors.white)),
        error: (_, __) =>
            const Text('Error', style: TextStyle(color: Colors.white)),
      ),
      actions: detailAsync.when(
        data: (detail) {
          if (detail == null) return [];
          final ticket = detail.ticket;

          return [
            /// Close button — shown only when ticket is active (not closed)
            if (ticket.isActive)
              IconButton(
                icon: const Icon(Icons.close, color: Colors.white),
                tooltip: 'Close Ticket',
                onPressed: _closeTicket,
              ),

            /// Reopen button — shown only when ticket is closed and can be reopened
            if (ticket.ticketStatus == 'closed' && detail.canReopen)
              IconButton(
                icon: const Icon(Icons.refresh, color: Colors.white),
                tooltip: 'Reopen Ticket',
                onPressed: _reopenTicket,
              ),
          ];
        },
        loading: () => [],
        error: (_, __) => [],
      ),
    );
  }

  /// Build the message list with reversed ListView (newest at bottom).
  /// Handles text, image, and system message types.
  Widget _buildMessageList(
      List<TicketMessageModel> messages, int userId, bool isDark) {
    if (messages.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.chat_bubble_outline, size: 80, color: Colors.grey[300]),
            const SizedBox(height: 16),
            Text(
              'No messages yet',
              style: TextStyle(
                fontSize: 16,
                color: isDark ? Colors.grey[400] : Colors.grey[600],
                fontWeight: FontWeight.w500,
              ),
            ),
            const SizedBox(height: 8),
            Text(
              'Send a message to start the conversation.',
              style: TextStyle(
                fontSize: 14,
                color: isDark ? Colors.grey[500] : Colors.grey[500],
              ),
            ),
          ],
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: _refreshMessages,
      child: ListView.builder(
        controller: _scrollController,
        reverse: true,
        physics: const AlwaysScrollableScrollPhysics(),
        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 16),
        itemCount: messages.length,
        itemBuilder: (context, index) {
          final message = messages[index];

          /// System messages — centered gray chip
          if (message.isSystem) {
            return _buildSystemMessage(message, isDark);
          }

          /// User or staff message bubble
          final isFromUser = message.isFromUser(userId);
          return _buildMessageBubble(message, isFromUser, isDark);
        },
      ),
    );
  }

  /// Build a system message chip (centered, gray background).
  /// Used for status change messages like "Ticket closed by admin".
  Widget _buildSystemMessage(TicketMessageModel message, bool isDark) {
    return Center(
      child: Container(
        margin: const EdgeInsets.symmetric(vertical: 8),
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
        decoration: BoxDecoration(
          color: isDark
              ? AppColors.surfaceDarkElevated
              : Colors.grey[200],
          borderRadius: BorderRadius.circular(16),
        ),
        child: Text(
          message.messageText ?? '',
          style: TextStyle(
            fontSize: 12,
            color: isDark ? Colors.grey[400] : Colors.grey[600],
            fontStyle: FontStyle.italic,
          ),
          textAlign: TextAlign.center,
        ),
      ),
    );
  }

  /// Build a message bubble for user or staff messages.
  /// User messages are right-aligned (indigo), staff messages left-aligned (gray).
  Widget _buildMessageBubble(
      TicketMessageModel message, bool isFromUser, bool isDark) {
    final bubbleColor = isFromUser
        ? AppColors.secondaryColor
        : (isDark ? AppColors.surfaceDarkElevated : Colors.grey[200]!);
    final textColor = isFromUser
        ? Colors.white
        : (isDark ? AppColors.fontColorDark : AppColors.fontColorLight);
    final senderColor = isFromUser
        ? Colors.white.withValues(alpha: 0.8)
        : (isDark ? AppColors.fontColorDarkMuted : AppColors.fontColorLightMuted);
    final timeColor = isFromUser
        ? Colors.white.withValues(alpha: 0.7)
        : (isDark ? Colors.grey[500]! : Colors.grey[500]!);

    return Align(
      alignment: isFromUser ? Alignment.centerRight : Alignment.centerLeft,
      child: Container(
        margin: EdgeInsets.only(
          left: isFromUser ? 64 : 8,
          right: isFromUser ? 8 : 64,
          top: 4,
          bottom: 4,
        ),
        child: Column(
          crossAxisAlignment:
              isFromUser ? CrossAxisAlignment.end : CrossAxisAlignment.start,
          children: [
            /// Sender name above the bubble
            if (message.sender != null)
              Padding(
                padding: const EdgeInsets.only(bottom: 2, left: 4, right: 4),
                child: Text(
                  message.sender!.displayName,
                  style: TextStyle(
                    fontSize: 11,
                    fontWeight: FontWeight.w500,
                    color: senderColor,
                  ),
                ),
              ),

            /// Message bubble container
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
              decoration: BoxDecoration(
                color: bubbleColor,
                borderRadius: BorderRadius.only(
                  topLeft: const Radius.circular(16),
                  topRight: const Radius.circular(16),
                  bottomLeft: Radius.circular(isFromUser ? 16 : 4),
                  bottomRight: Radius.circular(isFromUser ? 4 : 16),
                ),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  /// Image attachment (if present)
                  if (message.isImage && message.attachments.isNotEmpty)
                    _buildImageAttachment(message, isFromUser),

                  /// Text content
                  if (message.messageText != null &&
                      message.messageText!.isNotEmpty)
                    Text(
                      message.messageText!,
                      style: TextStyle(fontSize: 14, color: textColor),
                    ),

                  const SizedBox(height: 4),

                  /// Timestamp below the message
                  Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Text(
                        message.formattedTime,
                        style: TextStyle(fontSize: 10, color: timeColor),
                      ),
                      if (message.isEdited) ...[
                        const SizedBox(width: 4),
                        Text(
                          '(edited)',
                          style: TextStyle(fontSize: 10, color: timeColor),
                        ),
                      ],
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  /// Build an image attachment widget inside a message bubble.
  /// Shows a thumbnail that can be tapped to view the full image.
  Widget _buildImageAttachment(
      TicketMessageModel message, bool isFromUser) {
    final attachment = message.attachments.first;
    final rawUrl = attachment.thumbnailUrl ?? attachment.fileUrl;

    if (rawUrl == null) return const SizedBox.shrink();

    /* Backend returns relative /storage/... paths — prepend the origin URL */
    final imageUrl = rawUrl.startsWith('/')
        ? '${ApiConfig.storageBaseUrl}$rawUrl'
        : rawUrl;

    /* Full-size URL also needs the origin prefix if relative */
    final fullUrl = attachment.fileUrl != null && attachment.fileUrl!.startsWith('/')
        ? '${ApiConfig.storageBaseUrl}${attachment.fileUrl}'
        : attachment.fileUrl;

    return GestureDetector(
      /// Tap to view full-size image in a fullscreen popup with download and close
      onTap: () {
        if (fullUrl != null) {
          showDialog(
            context: context,
            barrierColor: Colors.black87,
            builder: (context) => Stack(
              children: [
                // Full-size image centered
                Center(
                  child: InteractiveViewer(
                    child: Image.network(
                      fullUrl,
                      fit: BoxFit.contain,
                      errorBuilder: (_, __, ___) =>
                          const Icon(Icons.broken_image, size: 48, color: Colors.white),
                    ),
                  ),
                ),
                // Top-right buttons: download + close
                Positioned(
                  top: MediaQuery.of(context).padding.top + 8,
                  right: 16,
                  child: Row(
                    children: [
                      // Close button
                      GestureDetector(
                        onTap: () => Navigator.of(context).pop(),
                        child: Container(
                          padding: const EdgeInsets.all(8),
                          decoration: BoxDecoration(
                            color: Colors.black54,
                            borderRadius: BorderRadius.circular(20),
                          ),
                          child: const Icon(Icons.close, color: Colors.white, size: 24),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          );
        }
      },
      child: Container(
        margin: const EdgeInsets.only(bottom: 8),
        constraints: const BoxConstraints(maxWidth: 200, maxHeight: 200),
        child: ClipRRect(
          borderRadius: BorderRadius.circular(8),
          child: Image.network(
            imageUrl,
            fit: BoxFit.cover,
            loadingBuilder: (context, child, progress) {
              if (progress == null) return child;
              return Container(
                width: 200,
                height: 150,
                color: Colors.grey[300],
                child: const Center(child: CircularProgressIndicator()),
              );
            },
            errorBuilder: (_, __, ___) => Container(
              width: 200,
              height: 150,
              color: Colors.grey[300],
              child: const Icon(Icons.broken_image, size: 48),
            ),
          ),
        ),
      ),
    );
  }

  /// Build the message input area at the bottom of the page.
  /// Hidden when the ticket is closed.
  Widget _buildInputArea(AsyncValue<TicketDetailResult?> detailAsync,
      TicketControllerState ticketState, bool isDark) {
    /// Check if ticket is closed — hide input if so
    final isClosed = detailAsync.when(
      data: (detail) => detail?.ticket.ticketStatus == 'closed',
      loading: () => false,
      error: (_, __) => false,
    );

    if (isClosed) {
      return Container(
        padding: const EdgeInsets.all(16),
        color: isDark ? AppColors.surfaceDark : Colors.grey[100],
        child: Center(
          child: Text(
            'This ticket is closed.',
            style: TextStyle(
              color: isDark ? Colors.grey[400] : Colors.grey[600],
              fontStyle: FontStyle.italic,
            ),
          ),
        ),
      );
    }

    /// Show send image button when image is selected
    if (_selectedImage != null) {
      return Container(
        padding: const EdgeInsets.all(16),
        color: isDark ? AppColors.surfaceDark : Colors.white,
        child: ElevatedButton(
          onPressed: ticketState.isUploadingImage ? null : _sendImageMessage,
          style: ElevatedButton.styleFrom(
            backgroundColor: AppColors.secondaryColor,
            foregroundColor: Colors.white,
            minimumSize: const Size(double.infinity, 48),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(12),
            ),
          ),
          child: ticketState.isUploadingImage
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
      );
    }

    /// Default text input with send and image picker buttons
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 8),
      color: isDark ? AppColors.surfaceDark : Colors.white,
      child: SafeArea(
        child: Row(
          children: [
            /// Image picker button
            IconButton(
              onPressed: _showImageSourceDialog,
              icon: Icon(
                Icons.image_outlined,
                color: isDark ? Colors.grey[400] : Colors.grey[600],
              ),
            ),

            /// Text input field
            Expanded(
              child: TextField(
                controller: _messageController,
                style: TextStyle(
                  color: isDark
                      ? AppColors.fontColorDark
                      : AppColors.fontColorLight,
                ),
                decoration: InputDecoration(
                  hintText: 'Type a message...',
                  hintStyle: TextStyle(
                    color: isDark
                        ? AppColors.fontColorDarkMuted
                        : AppColors.fontColorLightMuted,
                  ),
                  filled: true,
                  fillColor: isDark
                      ? AppColors.surfaceDarkElevated
                      : Colors.grey[100],
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(24),
                    borderSide: BorderSide.none,
                  ),
                  contentPadding: const EdgeInsets.symmetric(
                    horizontal: 16,
                    vertical: 10,
                  ),
                ),
                textInputAction: TextInputAction.send,
                onSubmitted: (_) => _sendTextMessage(),
              ),
            ),
            const SizedBox(width: 4),

            /// Send button
            IconButton(
              onPressed: ticketState.isSendingMessage ? null : _sendTextMessage,
              icon: ticketState.isSendingMessage
                  ? const SizedBox(
                      width: 20,
                      height: 20,
                      child: CircularProgressIndicator(strokeWidth: 2),
                    )
                  : const Icon(Icons.send, color: AppColors.primaryColor),
            ),
          ],
        ),
      ),
    );
  }

  /// Build skeleton loading state for messages
  Widget _buildSkeletonMessages(bool isDark) {
    return Skeletonizer(
      enabled: true,
      child: ListView.builder(
        reverse: true,
        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 16),
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
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
              decoration: BoxDecoration(
                color: isRight
                    ? AppColors.secondaryColor
                    : (isDark ? AppColors.surfaceDarkElevated : Colors.grey[200]),
                borderRadius: BorderRadius.circular(16),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Loading message placeholder text',
                    style: TextStyle(fontSize: 14, color: Colors.white),
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
          );
        },
      ),
    );
  }

  /// Build error state widget for when ticket detail fails to load
  Widget _buildErrorState(String message, bool isDark) {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.error_outline, size: 80, color: Colors.red.shade300),
          const SizedBox(height: 16),
          Text(
            message,
            style: TextStyle(
              fontSize: 16,
              color: isDark ? Colors.grey[400] : Colors.grey[700],
              fontWeight: FontWeight.w500,
            ),
          ),
          const SizedBox(height: 24),
          ElevatedButton.icon(
            onPressed: _refreshMessages,
            icon: const Icon(Icons.refresh),
            label: const Text('Retry'),
            style: ElevatedButton.styleFrom(
              backgroundColor: AppColors.primaryColor,
              foregroundColor: Colors.white,
              padding:
                  const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
