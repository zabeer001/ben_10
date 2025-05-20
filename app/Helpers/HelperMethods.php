<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class HelperMethods
{
    public static function uploadImage($image)
    {
        try {
            // Check if the image is valid
            if ($image && $image->isValid()) {
                // Define the destination path (public/tiles folder)
                $destinationPath = public_path('uploads');

                // Generate a unique filename for the image (optional)
                $imageName = time() . '_' . $image->getClientOriginalName();

                // Move the image to the tiles folder
                $image->move($destinationPath, $imageName);

                // Return the relative path to the image
                return 'uploads/' . $imageName;
            }

            // Return null if no image is uploaded or it is invalid
            return null;
        } catch (\Exception $e) {
            // Log the error if something goes wrong
            Log::error('Image upload failed: ' . $e->getMessage(), [
                'error' => $e->getTraceAsString(),
            ]);

            // Return null in case of an error
            return null;
        }
    }

    public static function updateImage($image, $oldImagePath = null)
    {
        if ($image && $image->isValid()) {
            // Delete the old image if it exists
            if ($oldImagePath && file_exists(public_path($oldImagePath))) {
                unlink(public_path($oldImagePath));
            }

            // Upload and return the new image path
            return self::uploadImage($image);
        }

        // Return the old image path if no new image is uploaded
        return $oldImagePath;
    }


}