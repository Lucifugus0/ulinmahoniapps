<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class VersionBumpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'version:bump {part=patch : Which part to bump (major, minor, or patch)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bump the application version number';

    /**
     * The path to the version file.
     *
     * @var string
     */
    protected $versionFile;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->versionFile = base_path('version');
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $part = strtolower($this->argument('part'));
        
        if (!in_array($part, ['major', 'minor', 'patch'])) {
            $this->error('Invalid version part. Must be one of: major, minor, patch');
            return 1;
        }

        // Read current version
        $currentVersion = '0.0.0';
        if (File::exists($this->versionFile)) {
            $currentVersion = trim(File::get($this->versionFile));
        }

        // Parse version
        $versionParts = explode('.', $currentVersion);
        if (count($versionParts) !== 3) {
            $this->error('Invalid version format. Expected x.y.z');
            return 1;
        }

        // Bump version
        list($major, $minor, $patch) = $versionParts;
        
        switch ($part) {
            case 'major':
                $major++;
                $minor = 0;
                $patch = 0;
                break;
            case 'minor':
                $minor++;
                $patch = 0;
                break;
            case 'patch':
                $patch++;
                break;
        }

        $newVersion = "$major.$minor.$patch";
        
        // Write new version
        File::put($this->versionFile, $newVersion);
        
        $this->info(sprintf('Version bumped from %s to %s', $currentVersion, $newVersion));
        
        return 0;
    }
}
