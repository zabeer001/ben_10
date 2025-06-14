<?php

namespace App\Http\Controllers;

use App\Helpers\HelperMethods;
use App\Models\ModelColorWiseImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ModelColorWiseImageController extends Controller
{
    public function __construct()
    {
        // Apply JWT authentication middleware only to store, update, and destroy methods
        $this->middleware('auth:api')->only(['store', 'update', 'destroy', 'statusUpdate']);
    }
    protected array $typeOfFields = ['imageFields', 'textFields'];

    protected array $imageFields = ['image','image2'];

    protected array $textFields = ['vehicle_model_id', 'color_1_id', 'color_2_id'];
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // return'oka';
        // Validate query parameters
        $validated = $request->validate([
            'id' => 'nullable|integer|min:1',
            'paginate_count' => 'nullable|integer|min:1',
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'model_id' => 'nullable|integer|min:1',
            'color_1_id' => 'nullable|integer|min:1',
            'color_2_id' => 'nullable|integer|min:1',
        ]);

        // Get query parameters
        $paginate_count = $validated['paginate_count'] ?? 10;
        $id = $validated['id'] ?? null;
        $search = $validated['search'] ?? null;
        $model_id = $validated['model_id'] ?? null;
        $color_1_id = $validated['color_1_id'] ?? null;
        $color_2_id = $validated['color_2_id'] ?? null;



        if ($id) {
            // return 'ok';
            $data = ModelColorWiseImage::with(['vehicleModel', 'color1', 'color2'])->find($id);
            if ($data) {
                return $data;
            } else {
                return response()->json(['message' => 'No data found'], 404);
            }
        }


        try {
            // Build the query

            //    $query = ModelColorWiseImage::query();

            $query = ModelColorWiseImage::with([
                'vehicleModel:id,name', // id is typically needed for the relationship
                'color1:id,name',
                'color2:id,name'
            ]);



            if ($model_id) {
                $query->where('vehicle_model_id', $model_id);
            }

            if ($color_1_id) {
                $query->where('color_1_id', $color_1_id);
            }

            if ($color_2_id) {
                $query->where('color_2_id', $color_2_id);
            }



            // Apply search filter
            if ($search) {
                $query->where('name', 'like', $search . '%');
            }

            // Paginate the result
            $data = $query->paginate($paginate_count);

            // Check if any data was returned
            if ($data->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No VehicleModels found',
                    'data' => [],
                ], 404);
            }

            // Return with pagination meta
            return response()->json([
                'success' => true,
                'message' => 'Theme retrieved successfully',
                'data' => $data,
                'current_page' => $data->currentPage(),
                'total_pages' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch VehicleModels.',
                'error' => $e->getMessage(),
            ], 500);
        }
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
        // return 0;
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
                                $data->$field = HelperMethods::updateImage($request->file($field), $data->$field);
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
            'image' => 'required',
            'image2' => 'nullable',
        ]);
    }
}
