<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

trait ImageUploadTrait
{
    public function storeImage(Request $request, $model, $fieldName = 'image', $folder = 'images')
    {
        if ($request->hasFile($fieldName)) {
            $result = Cloudinary::uploadApi()->upload($request->file($fieldName)->getRealPath(), [
                'folder' => $folder
            ]);
            $url = $result['secure_url'];
            try {
                // Manually create the Image model and associate it
                $image = new \App\Models\Image();
                $image->url = $url;
                $image->imageable_id = $model->id;
                $image->imageable_type = get_class($model);
                $image->save();
                \Log::info('Image created successfully for storeImage (manual).');
            } catch (\Exception $e) {
                \Log::error('Error creating image in storeImage (manual): ' . $e->getMessage());
            }
        } else {
            \Log::info('No file present for storeImage for model: ' . get_class($model));
        }
    }

    public function storeImages(Request $request, $model, $fieldName = 'images', $folder = 'images')
    {
        if ($request->hasFile($fieldName)) {
            foreach ($request->file($fieldName) as $imagefile) {
                $result = Cloudinary::uploadApi()->upload($imagefile->getRealPath(), [
                    'folder' => $folder
                ]);
                $url = $result['secure_url'];
                $model->images()->create(['url' => $url]);
            }
        }
    }

    public function updateImage(Request $request, $model, $fieldName = 'image', $folder = 'images')
    {
        if ($request->hasFile($fieldName)) {
            
            // Delete old image(s) from Cloudinary
            if ($model->image) {
                if ($model->image instanceof \Illuminate\Database\Eloquent\Collection) {
                    // morphMany
                    foreach ($model->image as $img) {
                        $publicId = $this->extractPublicId($img->getRawOriginal('url'));
                        if ($publicId) {
                            Cloudinary::uploadApi()->destroy($publicId);
                        }
                    }
                } else {
                    // morphOne
                    $publicId = $this->extractPublicId($model->image->getRawOriginal('url'));
                    if ($publicId) {
                        Cloudinary::uploadApi()->destroy($publicId);
                    }
                }
                $model->image()->delete();
            }

            // Store new image
            $result = Cloudinary::uploadApi()->upload($request->file($fieldName)->getRealPath(), [
                'folder' => $folder
            ]);
            $url = $result['secure_url'];
            try {
                // Manually create the Image model and associate it
                $image = new \App\Models\Image();
                $image->url = $url;
                $image->imageable_id = $model->id;
                $image->imageable_type = get_class($model);
                $image->save();
                \Log::info('Image created successfully for updateImage (manual).');
            } catch (\Exception $e) {
                \Log::error('Error creating image in updateImage (manual): ' . $e->getMessage());
            }
        } else {
            \Log::info('No file present for updateImage for model: ' . get_class($model));
        }
    }

    private function extractPublicId($url)
    {
        if (!$url) {
            return null;
        }
        $parts = parse_url($url);
        if (!isset($parts['path'])) {
            return null;
        }
        $path = $parts['path'];

        if (preg_match('/\/upload\/(?:v\d+\/)?(.+)/', $path, $matches)) {
            $publicId = $matches[1];
            // Remove file extension
            $publicId = preg_replace('/\.[^.]*$/', '', $publicId);
            return $publicId;
        }

        return null;
    }
}
