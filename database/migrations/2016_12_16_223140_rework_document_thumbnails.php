<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ReworkDocumentThumbnails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('docs', function (Blueprint $table) {
            $table->renameColumn('thumbnail', 'featuredImage');
        });

        $documents = DB::table('docs')->whereNotNull('featuredImage')->get();
        foreach ($documents as $document) {
            $imageId = str_random(12);

            $oldBaseImagePath = $this->getImagePathFromUrl($document->id, $document->featuredImage, true);

            $moves = [
                $oldBaseImagePath => $imageId.'-original',
            ];

            $sizes = config('madison.image_sizes');
            foreach ($sizes as $name => $size) {
                $sizedPath = $this->addSizeToImage($oldBaseImagePath, $size);
                $moves[$sizedPath] = $imageId.'-'.$name;
            }

            foreach ($moves as $oldLoc => $newLoc) {
                // check if file is present first
                if (Storage::exists($oldLoc)) {
                    Storage::move($oldLoc, $newLoc);
                }
            }

            DB
                ::table('docs')
                ->where('id', $document->id)
                ->update([
                    'featuredImage' => $imageId,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // no going back
    }

    public function getImagePathFromUrl($docId, $image, $unsized = false)
    {
        $image_url = str_replace('/documents/' . $docId . '/images/',
                                 'doc-' . $docId . '/',
                                 $image);

        // Remove any sizing.
        if ($unsized) {
            $image_url = $this->removeSizeFromImage($image_url);
        }

        return $image_url;
    }

    // This should work for any image - url, full path, or just the
    // base name.
    public function addSizeToImage($image, $size = null) {
        $size = $this->parseSizeName($size);

        // Just get the name part of the image.  We don't
        // want to accidentally
        // break up any paths that happen to have a dot in
        // them.
        $imageName = basename($image);

        if($size && preg_match('/^[0-9]{1,4}x[0-9]{1,4}$/', $size)) {
            // Insert the size string before the extension.
            // Only split this into two parts, in case of
            // multiple extensions.
            $imageParts = explode('.', $imageName, 2);
            $newImageName = $imageParts[0] . '-' . $size . '.' . $imageParts[1];

            // Replace the old image name with the new
            // image name.
            // This is more reliable than splitting the
            // path up.
            $image = str_replace($imageName, $newImageName, $image);

        }
        return $image;

    }

    public function removeSizeFromImage($image, $sizes = null)
    {
        if (!$sizes) {
            $sizes = config('madison.image_sizes');
        }

        // Just get the name part of the image.  We don't want to
        // accidentally
        // break up any paths that happen to have a dot in them.
        $imageName = basename($image);

        // Split on the first period, the beginning of the
        // extension.
        // Only split this into two parts, in case of multiple
        // extensions.
        $imageParts = explode('.', $imageName, 2);

        // Remove all possible image sizes.
        foreach ($sizes as $name=>$size) {
            $sizeName = $size['width'] . 'x' . $size['height'];
            $imageParts[0] = preg_replace('/-'.$sizeName.'$/', '', $imageParts[0]);

        }
        $newImageName = join('.', $imageParts);

        // Replace the old image name with the new image name.
        // This is more reliable than splitting the path up.
        return str_replace($imageName, $newImageName, $image);

    }

    // We allow a size array to be passed instead of a string, so we
    // build that here.
    private function parseSizeName($size)
    {
        if(is_array($size) && isset($size['width'], $size['height'])) {
            $size = $size['width'] . 'x' . $size['height'];
        }
        return $size;
    }
}
