<?php

namespace App\Http\Controllers;

use App\Helpers\HelperMethods;
use App\Models\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class ThemeController extends Controller
{
    protected array $typeOfFields = ['imageFields', 'textFields'];


    protected array $imageFields = [
        'image',
        'flooring_image',
        'cabinetry_1_image',
        'table_top_1_image',
        'seating_1_image',
        'cabinetry_2_image',
        'table_top_2_image',
        'seating_2_image',
    ];

    protected array $textFields = [
        'name',
        'flooring_name',
        'cabinetry_1_name',
        'table_top_1_name',
        'seating_1_name',
        'cabinetry_2_name',
        'table_top_2_name',
        'seating_2_name',
    ];
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Validate query parameters
        $validated = $request->validate([
            'id' => 'nullable|integer|min:1',
            'paginate_count' => 'nullable|integer|min:1',
            'search' => 'nullable|string|max:255',
            'category' => 'nullable|string|exists:categories,name',
            'color' => 'nullable|string|exists:colors,name',
            'status' => 'nullable|string|max:255',
        ]);

        // Get query parameters
        $paginate_count = $validated['paginate_count'] ?? 10;
        $id = $validated['id'] ?? null;
        $search = $validated['search'] ?? null;


        if ($id) {
            $data = Theme::find($id);
            if ($data) {
                return $data;
            } else {
                return response()->json(['message' => 'No data found'], 404);
            }
        }


        try {
            // Build the query

            $query = Theme::query();



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
        try {
            $validated = $this->validateRequest($request); // May throw ValidationException

            $theme = new Theme();

            foreach ($this->typeOfFields as $fieldType) {
                foreach ($this->{$fieldType} as $field) {
                    switch ($fieldType) {
                        case 'imageFields':
                            if ($request->hasFile($field)) {
                                $theme->$field = HelperMethods::uploadImage($request->file($field));
                            }
                            break;

                        case 'textFields':
                            if (isset($validated[$field])) {
                                $theme->$field = $validated[$field];
                            }
                            break;
                    }
                }
            }

            $theme->save();

            return $this->responseSuccess($theme, 'Theme created successfully', 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->responseError('Validation failed', $e->errors(), 422);

        } catch (\Exception $e) {
            Log::error('Error creating Theme: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'error' => $e->getTraceAsString(),
            ]);

            return $this->responseError('Something went wrong', $e->getMessage(), 500);
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(Theme $theme)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Theme $theme)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $this->validateRequest($request);

        try {
            $theme = Theme::findOrFail($id);

            // Use class properties here
            foreach ($this->typeOfFields as $fieldType) {
                foreach ($this->{$fieldType} as $field) {
                    switch ($fieldType) {
                        case 'imageFields':
                            if ($request->hasFile($field)) {
                                $theme->$field = HelperMethods::updateImage($request->file($field), $theme->$field);
                            }
                            break;

                        case 'textFields':
                            if (isset($validated[$field])) {
                                $theme->$field = $validated[$field];
                            }
                            break;
                    }
                }
            }

            $theme->save();

            return $this->responseSuccess($theme, 'Theme updated successfully', 200);
        } catch (\Exception $e) {
            Log::error('Error updating Theme: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'error' => $e->getTraceAsString(),
            ]);

            return $this->responseError('Something went wrong', $e->getMessage(), 500);
        }
    }




    /**
     * Handle image update - upload new image and delete old one if exists
     */

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Theme $theme)
    {
        try {
            // Delete associated images if they exist
            foreach ($this->typeOfFields as $fieldType) {
                if ($fieldType === 'imageFields') {
                    foreach ($this->{$fieldType} as $field) {
                        if ($theme->$field) {
                            HelperMethods::deleteImage($theme->$field);
                        }
                    }
                }
            }

            // Delete the record
            $theme->delete();

            return $this->responseSuccess(null, 'Data deleted successfully', 200);
        } catch (\Exception $e) {
            Log::error('Error deleting theme: ' . $e->getMessage(), [
                'theme_id' => $theme->id,
                'error' => $e->getTraceAsString(),
            ]);

            return $this->responseError('Something went wrong', $e->getMessage(), 500);
        }
    }

    private function validateRequest(Request $request)
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|file|max:10240',

            // Flooring
            'flooring_name' => 'required|string|max:255',
            'flooring_image' => 'nullable|file|max:10240',

            // Cabinetry 1
            'cabinetry_1_name' => 'required|string|max:255',
            'cabinetry_1_image' => 'nullable|file|max:10240',

            // Cabinetry 2
            'cabinetry_2_name' => 'required|string|max:255',
            'cabinetry_2_image' => 'nullable|file|max:10240',

            // Table Top 1
            'table_top_1_name' => 'required|string|max:255',
            'table_top_1_image' => 'nullable|file|max:10240',

            // Table Top 2
            'table_top_2_name' => 'required|string|max:255',
            'table_top_2_image' => 'nullable|file|max:10240',

            // Seating 1
            'seating_1_name' => 'required|string|max:255',
            'seating_1_image' => 'nullable|file|max:10240',

            // Seating 2
            'seating_2_name' => 'required|string|max:255',
            'seating_2_image' => 'nullable|file|max:10240',
        ]);
    }
}
