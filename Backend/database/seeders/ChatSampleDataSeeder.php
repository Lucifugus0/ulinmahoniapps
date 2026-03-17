<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Transaction;
use App\Models\User;

class ChatSampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Chat Sample Data Seeder...');

        // Get first user (Super Admin)
        $admin = User::find(1);
        if (!$admin) {
            $this->command->error('❌ User with ID 1 not found. Please ensure admin user exists.');
            return;
        }

        // Get first 3 transactions
        $transactions = Transaction::take(3)->get();

        if ($transactions->isEmpty()) {
            $this->command->error('❌ No transactions found. Please create transactions first.');
            return;
        }

        $this->command->info("✓ Found {$transactions->count()} transactions");

        foreach ($transactions as $index => $transaction) {
            $conversationNumber = $index + 1;

            $this->command->info("\n📝 Creating conversation #{$conversationNumber} for Order ID: {$transaction->order_id}");

            // Check if conversation already exists
            $existingConversation = ChatConversation::where('order_id', $transaction->order_id)->first();

            if ($existingConversation) {
                $this->command->warn("⚠️  Conversation for {$transaction->order_id} already exists. Skipping...");
                continue;
            }

            // Create conversation
            $conversation = ChatConversation::create([
                'order_id' => $transaction->order_id,
                'property_id' => $transaction->property_id ?? 1,
                'title' => "Chat untuk Booking {$transaction->order_id}",
                'status' => $index === 2 ? 'archived' : 'active',
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ]);

            $this->command->info("  ✓ Conversation created (ID: {$conversation->id})");

            // Add admin as staff participant
            $conversation->addParticipant($admin->id, 'staff');
            $this->command->info("  ✓ Added admin as staff participant");

            // Add customer participant if exists
            if ($transaction->user_id) {
                $conversation->addParticipant($transaction->user_id, 'customer');
                $this->command->info("  ✓ Added customer participant");
            }

            // Create sample messages
            $messages = $this->getSampleMessages($conversationNumber);

            foreach ($messages as $messageIndex => $messageData) {
                $message = ChatMessage::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $messageData['sender_id'] ?? $admin->id,
                    'message_text' => $messageData['text'],
                    'message_type' => 'text',
                    'created_by' => $messageData['sender_id'] ?? $admin->id,
                    'updated_by' => $messageData['sender_id'] ?? $admin->id,
                    'created_at' => now()->subMinutes(30 - ($messageIndex * 5)),
                ]);
            }

            $this->command->info("  ✓ Created " . count($messages) . " sample messages");

            // Update last_message_at
            $conversation->update(['last_message_at' => now()->subMinutes(5)]);
        }

        $this->command->info("\n✅ Chat sample data created successfully!");
        $this->command->info("🔗 Access chat at: /chat");
    }

    /**
     * Get sample messages for conversation
     */
    protected function getSampleMessages($conversationNumber): array
    {
        $admin = User::find(1);

        $messageSets = [
            // Conversation 1: Customer inquiry about check-in
            1 => [
                [
                    'text' => 'Halo, saya mau tanya tentang check-in untuk booking saya.',
                    'sender_id' => $admin->id, // Simulating customer
                ],
                [
                    'text' => 'Halo! Terima kasih sudah menghubungi kami. Boleh saya bantu untuk check-in Anda?',
                    'sender_id' => $admin->id,
                ],
                [
                    'text' => 'Check-in normal jam berapa ya? Dan apakah bisa early check-in?',
                    'sender_id' => $admin->id,
                ],
                [
                    'text' => 'Check-in normal kami mulai jam 14:00. Untuk early check-in bisa diatur jam 12:00 dengan tambahan biaya Rp 50.000. Apakah Anda berminat?',
                    'sender_id' => $admin->id,
                ],
                [
                    'text' => 'Oke baik, saya ambil early check-in jam 12:00. Bagaimana cara pembayarannya?',
                    'sender_id' => $admin->id,
                ],
            ],

            // Conversation 2: Payment confirmation
            2 => [
                [
                    'text' => 'Saya sudah transfer untuk booking saya. Mohon dicek ya.',
                    'sender_id' => $admin->id,
                ],
                [
                    'text' => 'Baik, terima kasih. Saya akan cek pembayaran Anda sekarang.',
                    'sender_id' => $admin->id,
                ],
                [
                    'text' => 'Pembayaran Anda sudah kami terima dan booking sudah dikonfirmasi. Terima kasih!',
                    'sender_id' => $admin->id,
                ],
            ],

            // Conversation 3: Room facility question
            3 => [
                [
                    'text' => 'Apakah kamar sudah termasuk sarapan?',
                    'sender_id' => $admin->id,
                ],
                [
                    'text' => 'Halo! Untuk tipe kamar yang Anda booking belum termasuk sarapan. Namun kami menyediakan breakfast package Rp 35.000/orang.',
                    'sender_id' => $admin->id,
                ],
                [
                    'text' => 'Baik, terima kasih infonya.',
                    'sender_id' => $admin->id,
                ],
            ],
        ];

        return $messageSets[$conversationNumber] ?? [];
    }
}
