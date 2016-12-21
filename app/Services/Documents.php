<?php

namespace App\Services;

use App\Models\Document;
use Image;
use Log;
use Storage;

class Documents
{
    public function getAllImageSizes()
    {
        $sizes = config('madison.image_sizes');
        $sizes['original'] = [
            'height' => null,
            'width' => null,
            'crop' => false,
        ];

        return $sizes;
    }

    public function generateAllImageSizes($file)
    {
        $imageId = str_random(12);

        // Save the multiple sizes of this image.
        $sizes = $this->getAllImageSizes();

        foreach ($sizes as $name => $size) {
            $img = Image::make($file);
            if (!is_null($size['width']) || !is_null($size['height'])) {
                if ($size['crop']) {
                    $img->fit($size['width'], $size['height']);
                } else {
                    $img->resize($size['width'], $size['height']);
                }
            }

            Storage::put(
                $this->getImageIdForSize($imageId, $name),
                $img->stream()->__toString()
            );

            // destroys image instance, not the base file
            $img->destroy();
        }

        return $imageId;
    }

    public function destroyAllImageSizes($imageId)
    {
        $sizes = $this->getAllImageSizes();

        foreach ($sizes as $name => $size) {
            $imagePath = $this->getImageIdForSize($imageId, $name);
            if (Storage::has($imagePath)) {
                try {
                    Storage::delete($imagePath);
                } catch (Exception $e) {
                    Log::error("Error deleting document image for document id {$document->id} and size ${size}");
                    Log::error($e);
                }
            }
        }
    }

    public function getImageIdForSize($imageId, $size = 'original')
    {
        if (empty($size)) {
            $size = 'original';
        }

        return $imageId . '-' . $size;
    }

    // We allow a size array to be passed instead of a string, so we build that here.
    private function parseSizeName($size)
    {
        if(is_array($size) && isset($size['width'], $size['height'])) {
            $size = $size['width'] . 'x' . $size['height'];
        }
        return $size;
    }
}
