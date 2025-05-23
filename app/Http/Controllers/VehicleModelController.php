<?php

namespace App\Http\Controllers;

use App\Helpers\HelperMethods;
use App\Models\VehicleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class VehicleModelController extends Controller
{
    public function __construct()
    {
        // Apply JWT authentication middleware only to store, update, and destroy methods
        $this->middleware('auth:api')->only(['store', 'update', 'destroy', 'statusUpdate']);
    }


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
        $category = $validated['category'] ?? null;
        $status = $validated['status'] ?? null;

        if ($id) {
           $data = VehicleModel::with('categories')->find($id);
            if ($data) {
                return $data;
            } else {
                return response()->json(['message' => 'No data found'], 404);
            }
        }
        try {
            // Build the query



            $query = VehicleModel::with(['categories']);

            // Apply search filter
            if ($search) {
                $query->where('name', 'like', $search . '%');
            }

            // Apply category filter
            if ($category) {
                $query->whereHas('categories', function ($q) use ($category) {
                    $q->where('name', $category);
                });
            }



            if ($status) {
                $query->where('status', 'like', $status . '%');
            }


            // Paginate the result
            $VehicleModels = $query->paginate($paginate_count);

            // Check if any data was returned
            if ($VehicleModels->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No VehicleModels found',
                    'data' => [],
                ], 404);
            }

            // Return with pagination meta
            return response()->json([
                'success' => true,
                'message' => 'VehicleModels retrieved successfully',
                'data' => $VehicleModels,
                'current_page' => $VehicleModels->currentPage(),
                'total_pages' => $VehicleModels->lastPage(),
                'per_page' => $VehicleModels->perPage(),
                'total' => $VehicleModels->total(),
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
     * Store a newly created tile in storage.
     */
    public function store(Request $request)
    {

        // dd($request);
        // Validate the incoming request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sleep_person' => 'required|string',
            'description' => 'required',
            'inner_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:10240', // 10 MB = 10240 KB
            'category_id' => 'required',
            // 'color_id' => 'nullable|array', // Added color_id validation
            // 'color_id.*' => 'exists:colors,id', // Ensure color_id exists in colors table
            'price' => 'required|numeric|min:0',
            'base_price' => 'required|numeric|min:0',
        ]);
        // return 'ok';

        try {
            // Handle image upload if present
            $imagePath = null;
            if ($request->hasFile('inner_image')) {
                $imagePath = HelperMethods::uploadImage($request->file('inner_image'));
            }

            // Create a new tile
            $VehicleModel = VehicleModel::create([
                'name' => $validated['name'],
                'sleep_person' => $validated['sleep_person'],
                'description' => $validated['description'],
                // 'image_svg_text' => $validated['image_svg_text'],
                'inner_image' => $imagePath,
                'category_id' => $validated['category_id'], // set the relationship
                'base_price' => $validated['base_price'],
                'price' => $validated['price'],
            ]);

            // Sync relationships
            // $VehicleModel->categories()->sync($validated['category_id']); set the relationship here
            // $VehicleModel->colors()->sync($validated['color_id'] ?? []);

            return $this->responseSuccess(
                // $VehicleModel->load(['categories', 'colors']),
                $VehicleModel,
                'VehicleModel created successfully',
                201
            );
        } catch (\Exception $e) {
            Log::error('Error creating VehicleModel: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'error' => $e->getTraceAsString(),
            ]);

            return $this->responseError('Something went wrong', $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(VehicleModel $vehicleModel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VehicleModel $vehicleModel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        // Validate the incoming request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sleep_person' => 'required|string',
            'description' => 'required',
            'inner_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'category_id' => 'required',
            'price' => 'nullable|numeric|min:0',
            'base_price' => 'required|numeric|min:0',
        ]);
        //  return 'ok';
        try {
            $vehicleModel = VehicleModel::findOrFail($id); // Find model or fail
            return $vehicleModel;

            // Handle image upload if present
            if ($request->hasFile('inner_image')) {
                $imagePath = HelperMethods::updateImage($request->file('inner_image'));
                $vehicleModel->inner_image = $imagePath;
            }

            // Update the model fields
            $vehicleModel->name = $validated['name'];
            $vehicleModel->sleep_person = $validated['sleep_person'];
            $vehicleModel->description = $validated['description'];
            $vehicleModel->category_id = $validated['category_id'];
            $vehicleModel->price = $validated['price'];
            $vehicleModel->base_price = $validated['base_price'];
            $vehicleModel->save();

            return $this->responseSuccess(
                $vehicleModel->load('category'), // note: 'category', not 'categories'
                'Tile updated successfully',
                200
            );
        } catch (\Exception $e) {
            Log::error('Error updating tile: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'error' => $e->getTraceAsString(),
            ]);

            return $this->responseError('Something went wrong', $e->getMessage(), 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VehicleModel $vehicleModel)
    {
        try {
            // Delete associated image file if it exists
            if ($vehicleModel->inner_image && file_exists(public_path($vehicleModel->inner_image))) {
                unlink(public_path($vehicleModel->inner_image));
            }

            // Delete the vehicle model
            $vehicleModel->delete();

            Log::info('VehicleModel deleted', ['vehicle_model_id' => $vehicleModel->id]);

            return $this->responseSuccess(null, 'Vehicle model deleted successfully', 200);
        } catch (\Exception $e) {
            Log::error('Error deleting vehicle model: ' . $e->getMessage(), [
                'vehicle_model_id' => $vehicleModel->id,
                'error' => $e->getTraceAsString(),
            ]);

            return $this->responseError('Failed to delete vehicle model', $e->getMessage(), 500);
        }
    }
}
