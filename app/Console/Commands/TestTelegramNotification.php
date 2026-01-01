<?php

namespace App\Console\Commands;

use App\Services\Notification\TelegramService;
use Illuminate\Console\Command;

class TestTelegramNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:test {--message= : Custom message to send}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Telegram notification by sending a message to admin channel';

    /**
     * Execute the console command.
     */
    public function handle(TelegramService $telegramService)
    {
        $this->info('Testing Telegram Bot Connection...');
        $this->newLine();

        // Get bot info
        $botInfo = $telegramService->getBotInfo();
        
        if ($botInfo['success']) {
            $this->info('✅ Bot connected successfully!');
            $bot = $botInfo['data']['result'] ?? [];
            $this->table(
                ['Property', 'Value'],
                [
                    ['Bot Username', '@' . ($bot['username'] ?? 'N/A')],
                    ['Bot Name', $bot['first_name'] ?? 'N/A'],
                    ['Bot ID', $bot['id'] ?? 'N/A'],
                ]
            );
        } else {
            $this->error('❌ Failed to connect to bot: ' . ($botInfo['message'] ?? 'Unknown error'));
            $this->newLine();
            $this->warn('Please check your TELEGRAM_BOT_TOKEN in .env file');
            return 1;
        }

        $this->newLine();
        $this->info('Sending test message to admin channel...');

        // Send test message
        $message = $this->option('message') ?: $this->getDefaultTestMessage();
        $result = $telegramService->sendToAdminChannel($message);

        if ($result['success']) {
            $this->info('✅ Message sent successfully!');
            $this->info('Message ID: ' . ($result['message_id'] ?? 'N/A'));
        } else {
            $this->error('❌ Failed to send message: ' . ($result['message'] ?? 'Unknown error'));
            $this->newLine();
            $this->warn('Make sure:');
            $this->warn('1. The bot is added to the channel as admin');
            $this->warn('2. The TELEGRAM_ADMIN_CHANNEL is correct (should start with -100 for channels)');
            return 1;
        }

        $this->newLine();
        $this->info('Test completed successfully!');
        
        return 0;
    }

    /**
     * Get default test message
     */
    protected function getDefaultTestMessage(): string
    {
        return "🔔 <b>TEST NOTIFICATION</b>\n\n"
            . "This is a test message from <b>LVTN Cinema Booking System</b>.\n\n"
            . "📅 Sent at: " . date('d/m/Y H:i:s') . "\n"
            . "🖥️ Server: " . gethostname() . "\n"
            . "✅ If you see this message, Telegram notifications are working correctly!";
    }
}
