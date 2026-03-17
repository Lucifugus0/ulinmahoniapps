<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateApiKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-api-key';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new API key and update .env file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $key = 'API_KEY=' . Str::random(32);
        
        $envFile = base_path('.env');
        
        if (File::exists($envFile)) {
            $content = File::get($envFile);
            
            // Remove existing API_KEY if it exists
            $content = preg_replace('/^API_KEY=.*/m', '', $content);
            
            // Add new API_KEY
            $content .= "\n" . $key . "\n";
            
            File::put($envFile, $content);
            
            $this->info('API key generated successfully!');
            $this->line('<comment>API Key:</comment> ' . str_replace('API_KEY=', '', $key));
            $this->line('Please restart your application for changes to take effect.');
        } else {
            $this->error('.env file not found!');
        }
    }
}
