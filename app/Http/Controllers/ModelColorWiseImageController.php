<?php

namespace App\Http\Controllers;

use App\Helpers\HelperMethods;
use App\Models\ModelColorWiseImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ModelColorWiseImageController extends Controller
{
    protected array $typeOfFields = ['imageFields', 'textFields'];

    protected array $imageFields = ['image'];

    protected array $textFields = ['vehicle_model_id', 'color_1_id', 'color_2_id'];
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**MM
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        try {
            $data = new ModelColorWiseImage();

            // Process fields
            foreach ($this->typeOfFields as $fieldType) {
                foreach ($this->{$fieldType} as $field) {
                    switch ($fieldType) {
                        case 'imageFields':
                            if ($request->hasFile($field)) {
                                $data->$field = HelperMethods::uploadImage($request->file($field));
                            }
                            break;

                        case 'textFields':
                            if (isset($validated[$field])) {
                                $data->$field = $validated[$field];
                            }
                            break;
                    }
                }
            }

            $data->save();

            return $this->responseSuccess($data, 'Data created successfully', 201);
        } catch (\Exception $e) {
            Log::error('Error creating model color wise image: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'error' => $e->getTraceAsString(),
            ]);
            return $this->responseError('Something went wrong', $e->getMessage(), 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(ModelColorWiseImage $modelColorWiseImage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ModelColorWiseImage $modelColorWiseImage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ModelColorWiseImage $modelColorWiseImage)
    {
        $validated = $this->validateRequest($request);

        try {
            $data = $modelColorWiseImage;

            // Process fields
            foreach ($this->typeOfFields as $fieldType) {
                foreach ($this->{$fieldType} as $field) {
                    switch ($fieldType) {
                        case 'imageFields':
                            if ($request->hasFile($field)) {
                                if ($data->$field) {
                                    HelperMethods::deleteImage($data->$field);
                                }
                                $data->$field = HelperMethods::uploadImage($request->file($field));
                            }
                            break;

                        case 'textFields':
                            if (isset($validated[$field])) {
                                $data->$field = $validated[$field];
                            }
                            break;
                    }
                }
            }

            $data->save();

            return $this->responseSuccess($data, 'Data updated successfully', 200);
        } catch (\Exception $e) {
            Log::error('Error updating model color wise image: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'model_color_wise_image_id' => $modelColorWiseImage->id,
                'error' => $e->getTraceAsString(),
            ]);
            return $this->responseError('Something went wrong', $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ModelColorWiseImage $modelColorWiseImage)
    {
        try {
            // Delete associated images if they exist
            foreach ($this->typeOfFields as $fieldType) {
                if ($fieldType === 'imageFields') {
                    foreach ($this->{$fieldType} as $field) {
                        if ($modelColorWiseImage->$field) {
                            HelperMethods::deleteImage($modelColorWiseImage->$field);
                        }
                    }
                }
            }

            // Delete the record
            $modelColorWiseImage->delete();

            return $this->responseSuccess(null, 'Data deleted successfully', 200);
        } catch (\Exception $e) {
            Log::error('Error deleting model color wise image: ' . $e->getMessage(), [
                'model_color_wise_image_id' => $modelColorWiseImage->id,
                'error' => $e->getTraceAsString(),
            ]);
            return $this->responseError('Something went wrong', $e->getMessage(), 500);
        }
    }
    protected function validateRequest($request)
    {
        return $request->validate([
            'vehicle_model_id' => 'required|integer',
            'color_1_id' => 'required|integer',
            'color_2_id' => 'nullable|integer',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    }
}
