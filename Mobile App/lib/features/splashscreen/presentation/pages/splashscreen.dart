// import 'package:flutter/material.dart';
// import 'package:video_player/video_player.dart';
// import 'package:go_router/go_router.dart';
// class SplashScreen extends StatefulWidget {
//   const SplashScreen({Key? key}) : super(key: key);
//
//   @override
//   State<SplashScreen> createState() => _SplashScreenState();
// }
//
// class _SplashScreenState extends State<SplashScreen> {
//   late VideoPlayerController _controller;
//
//   @override
//   void initState() {
//     super.initState();
//
//     _controller = VideoPlayerController.asset("assets/videos/intro.mp4")
//       ..initialize().then((_) {
//         setState(() {});
//         _controller.play();
//       })
//       ..setLooping(false);
//
//     _controller.addListener(() {
//       final isVideoEnded = _controller.value.position >= _controller.value.duration;
//       if (isVideoEnded) {
//         // Navigasi ke halaman home menggunakan GoRouter
//         context.go('/home');
//       }
//     });
//   }
//
//   @override
//   void dispose() {
//     _controller.dispose();
//     super.dispose();
//   }
//
//   @override
//   Widget build(BuildContext context) {
//     return Scaffold(
//       backgroundColor: Colors.black,
//       body: Center(
//         child: _controller.value.isInitialized
//             ? AspectRatio(
//           aspectRatio: _controller.value.aspectRatio,
//           child: VideoPlayer(_controller),
//         )
//             : const CircularProgressIndicator(),
//       ),
//     );
//   }
// }
