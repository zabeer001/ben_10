<?php

namespace App\Http\Controllers;

use App\Helpers\HelperMethods;
use App\Models\ModelColorWiseImage;
use App\Models\ModelThemeWiseImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ModelThemeWiseImageController extends Controller
{
    public function __construct()
    {
        // Apply JWT authentication middleware only to store, update, and destroy methods
        $this->middleware('auth:api')->only(['store', 'update', 'destroy', 'statusUpdate']);
    }

    protected array $typeOfFields = ['imageFields', 'textFields'];


    protected array $imageFields = [
        'image',
    ];

    protected array $textFields = [
        'vehicle_model_id',
        'theme_id',
    ];
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
            'theme_id' => 'nullable|integer|min:1',
        ]);

        // Get query parameters
        $paginate_count = $validated['paginate_count'] ?? 10;
        $id = $validated['id'] ?? null;
        $search = $validated['search'] ?? null;
        $model_id = $validated['model_id'] ?? null;
        $theme_id = $validated['theme_id'] ?? null;


        if ($id) {
            // return 'ok';
            $data = ModelThemeWiseImage::with(['vehicleModel', 'theme'])->find($id);
            if ($data) {
                return $data;
            } else {
                return response()->json(['message' => 'No data found'], 404);
            }
        }



        try {
            // Build the query

            //    $query = ModelColorWiseImage::query();

            $query = ModelThemeWiseImage::with([
                'vehicleModel:id,name', // id is typically needed for the relationship
                'theme:id,name'
            ]);



            // Apply search filter
            if ($search) {
                $query->where('name', 'like', $search . '%');
            }

            if ($model_id) {
                $query->where('vehicle_model_id', $model_id);
            }

            if ($theme_id) {
                $query->where('theme_id', $theme_id);
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

    /**
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
            $data = new ModelThemeWiseImage();

            // Use class properties here
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

            return $this->responseSuccess($data, 'data created successfully', 201);
        } catch (\Exception $e) {
            Log::error('Error creating data: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'error' => $e->getTraceAsString(),
            ]);

            return $this->responseError('Something went wrong', $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ModelThemeWiseImage $modelThemeWiseImage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ModelThemeWiseImage $modelThemeWiseImage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ModelThemeWiseImage $modelThemeWiseImage)
    {
        $validated = $this->validateRequest($request);

        try {
            $data = $modelThemeWiseImage;

            // Use class properties here
            foreach ($this->typeOfFields as $fieldType) {
                foreach ($this->{$fieldType} as $field) {
                    switch ($fieldType) {
                        case 'imageFields':
                            if ($request->hasFile($field)) {
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

            return $this->responseSuccess($data, 'data created successfully', 201);
        } catch (\Exception $e) {
            Log::error('Error creating data: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'error' => $e->getTraceAsString(),
            ]);

            return $this->responseError('Something went wrong', $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ModelThemeWiseImage $modelThemeWiseImage)
    {
        try {
            // Delete associated images if they exist
            foreach ($this->typeOfFields as $fieldType) {
                if ($fieldType === 'imageFields') {
                    foreach ($this->{$fieldType} as $field) {
                        if ($modelThemeWiseImage->$field) {
                            HelperMethods::deleteImage($modelThemeWiseImage->$field);
                        }
                    }
                }
            }

            // Delete the record
            $modelThemeWiseImage->delete();

            return $this->responseSuccess(null, 'Data deleted successfully', 200);
        } catch (\Exception $e) {
            Log::error('Error deleting data: ' . $e->getMessage(), [
                'model_id' => $modelThemeWiseImage->id,
                'error' => $e->getTraceAsString(),
            ]);

            return $this->responseError('Something went wrong', $e->getMessage(), 500);
        }
    }

    protected function validateRequest($request)
    {
        return $request->validate([
            'vehicle_model_id' => 'required|integer',
            'theme_id' => 'nullable|integer',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    }

}
