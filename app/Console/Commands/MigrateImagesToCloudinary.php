<?php

namespace App\Console\Commands;

use App\Models\Image;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateImagesToCloudinary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:migrate-to-cloudinary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing local images to Cloudinary';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting image migration to Cloudinary...');

        $this->info(json_encode(config('cloudinary')));

        $images = Image::all();
        $progressBar = $this->output->createProgressBar($images->count());

        foreach ($images as $image) {
            $rawUrl = $image->getRawOriginal('url');
            if (!$rawUrl) {
                $this->line("Skipping image ID: {$image->id}, URL is empty.");
                $progressBar->advance();
                continue;
            }

            if (!filter_var($rawUrl, FILTER_VALIDATE_URL)) {
                if (Storage::disk('public')->exists($rawUrl)) {
                    try {
                        $fileContent = Storage::disk('public')->get($rawUrl);
                        
                        // Define a folder in Cloudinary, e.g., using the imageable type
                        $folder = $image->imageable_type ? class_basename($image->imageable_type) : 'uncategorized';

                        // Create a file in memory before uploading
                        $tempFile = tmpfile();
                        fwrite($tempFile, $fileContent);
                        $tempFilePath = stream_get_meta_data($tempFile)['uri'];

                        // Upload to Cloudinary
                        $newUrl = \Cloudinary::upload($tempFilePath, [
                            'folder' => $folder,
                            'use_filename' => true,
                            'unique_filename' => true
                        ])->getSecurePath();

                        fclose($tempFile);

                        // Update the image URL in the database
                        $image->update(['url' => $newUrl]);

                        $this->info("Migrated image ID: {$image->id} to {$newUrl}");

                    } catch (\Exception $e) {
                        $this->error("Could not migrate image ID: {$image->id}. Error: " . $e->getMessage());
                    }
                } else {
                    $this->warn("Local file not found for image ID: {$image->id}. Path: {$rawUrl}");
                }
            } else {
                $this->line("Skipping image ID: {$image->id}, already a URL.");
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->info("\nImage migration completed!");
    }
}
