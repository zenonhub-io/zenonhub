<?php

namespace App\Jobs;

use Storage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateMaxmindDb implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public function handle(): void
    {
        $citiesDb      = 'https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&license_key=NEEv1mKCYo46J0oy&suffix=tar.gz';
        $storageFolder = storage_path("app");
        $baseDir       = 'maxmind';
        $filename      = 'GeoLite2-City.mmdb';
        $downloadPath  = "$storageFolder/{$baseDir}/GeoLite2-City.tar.gz";
        $archivePath   = "$storageFolder/{$baseDir}/GeoLite2-City.tar";

        Storage::delete("{$baseDir}/{$filename}");

        $contents = file_get_contents($citiesDb);
        Storage::put("{$baseDir}/GeoLite2-City.tar.gz", $contents);

        // Un-compress file
        $download = new \PharData($downloadPath);
        $download->decompress();

        // Extract file
        $archive = new \PharData($archivePath);
        $archive->extractTo("{$storageFolder}/{$baseDir}");

        // Delete original download
        unlink($downloadPath);
        unlink($archivePath);

        // Gets array of all directories
        $files = Storage::directories($baseDir);

        if (is_array($files)) {
            // Moves the database file
            Storage::move("{$files[0]}/{$filename}", "{$baseDir}/{$filename}");

            // Delete leftover directories
            Storage::deleteDirectory($files[0]);
        }
    }
}
