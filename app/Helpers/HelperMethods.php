<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Str;  // Add this import to use Str

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

    public static function deleteImage($oldImagePath)
    {
        try {
            // Check if the image path exists and is not empty
            if ($oldImagePath && file_exists(public_path($oldImagePath))) {
                unlink(public_path($oldImagePath));
                return true;
            }

            // Return true if no image to delete (no action needed)
        } catch (\Exception $e) {
            Log::error('Error deleting image: ' . $e->getMessage(), [
                'image_path' => $oldImagePath,
                'error' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }


    public static function populateModelFields($model, $request, $validated, $fieldTypes, $fieldGroups)
    {
        foreach ($fieldTypes as $fieldType) {
            foreach ($fieldGroups[$fieldType] as $field) {
                switch ($fieldType) {
                    case 'imageFields':
                        if ($request->hasFile($field)) {
                            $model->$field = isset($model->$field)
                                ? self::updateImage($request->file($field), $model->$field)
                                : self::uploadImage($request->file($field));
                        }
                        break;

                    case 'textFields':
                    case 'numericFields':
                        if (isset($validated[$field])) {
                            $model->$field = $validated[$field];
                        }
                        break;
                }
            }
        }

        return $model;
    }

    public static function generateUniqueId(int $length = 40): string
    {
        $uuid = Str::uuid()->toString();
        $cleanUuid = str_replace('-', '', $uuid);
        $uniqId = $cleanUuid . time();

        if (strlen($uniqId) > $length) {
            $uniqId = substr($uniqId, -$length);
        }

        return $uniqId;
    }


}