import 'package:flutter/material.dart';
import 'options.dart';

Widget faq() {
  return Column(
    children: [
      const SizedBox(height: 8),
      SingleChildScrollView(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 12),
        child:
        OptionsSelector(
          options: ["Umum", "Akun", "Pembayaran", "Layanan"],
          onSelected: (value) {

          },
        ),

      ),
      Padding(
        padding: const EdgeInsets.all(16.0),
        child: TextField(
          decoration: InputDecoration(
            hintText: "Cari bantuan", 
            hintStyle: const TextStyle(color: Colors.white70),
            filled: true,
            fillColor: const Color(0xFF134E3A),
            prefixIcon: const Icon(Icons.search, color: Colors.white),
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: BorderSide.none,
            ),
          ),
          style: const TextStyle(color: Colors.white),
        ),
      ),
      Expanded(child: _faqData()),
    ],
  );
}

Widget _faqCard({
  required String questions,
  required String answers,
}) {
  return Card(
    color: const Color(0xFF134E3A),
    margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
    child: ExpansionTile(
      iconColor: Colors.white,
      collapsedIconColor: Colors.white,
      title: Text(
        questions,
        style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
      ),
      children: [
        Padding(
          padding: const EdgeInsets.all(16.0),
          child: Text(answers, style: const TextStyle(color: Colors.white)),
        ),
      ],
    ),
  );
}

Widget _faqData() {
  return ListView(
    children: [
      _faqCard(
        questions: "Bagaimana cara mengelola notifikasi saya?", 
        answers: "Untuk mengelola notifikasi, pergi ke \"Pengaturan,\" pilih \"Pengaturan Notifikasi,\" dan sesuaikan preferensi Anda.", 
      ),
      _faqCard(
        questions: "Bagaimana cara memulai sesi meditasi terpandu?", 
        answers: "Buka tab meditasi dan pilih sesi untuk memulai.", 
      ),
      _faqCard(
        questions: "Bagaimana cara bergabung dengan grup dukungan?", 
        answers: "Pergi ke Komunitas > Grup Dukungan dan ketuk Gabung pada grup pilihan Anda.", 
      ),
      _faqCard(
        questions: "Apakah data saya aman dan pribadi?", 
        answers: "Ya, kami mengikuti enkripsi standar industri dan praktik privasi untuk menjaga keamanan data Anda.", 
      ),
    ],
  );
}