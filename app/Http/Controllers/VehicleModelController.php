<?php

namespace App\Http\Controllers;

use App\Helpers\HelperMethods;
use App\Models\VehicleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;


class VehicleModelController extends Controller
{
    public function __construct()
    {
        // Apply JWT authentication middleware only to store, update, and destroy methods
        $this->middleware('auth:api')->only(['store', 'update', 'destroy', 'statusUpdate']);
    }

    protected $typeOfFields = ['imageFields', 'textFields', 'numericFields'];

    protected $imageFields = ['inner_image', 'outer_image'];

    protected $textFields = ['name', 'sleep_person', 'description'];

    protected $numericFields = ['price', 'base_price', 'category_id'];




    protected function validateRequest(Request $request)
    {
        return $request->validate([
            'name' => 'sometimes|string|max:255',
            'sleep_person' => 'sometimes|string',
            'description' => 'sometimes|string',
            'inner_image' => 'nullable|file|max:50240', // 10 MB = 10240 KB
            'outer_image' => 'nullable|file|max:50240', // 10 MB = 10240 KB
            'category_id' => 'sometimes|exists:categories,id',
            'price' => 'sometimes|numeric|min:0',
            'base_price' => 'sometimes|numeric|min:0',
        ]);
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

        // Extract validated parameters with default values
        $paginate_count = $validated['paginate_count'] ?? 10;
        $id = $validated['id'] ?? null;
        $search = $validated['search'] ?? null;
        $category = $validated['category'] ?? null;
        $color = $validated['color'] ?? null;
        $status = $validated['status'] ?? null;

        // Fetch by ID if provided
        if ($id) {
            $data = VehicleModel::with(['category', 'colors'])->find($id);
            if ($data) {
                return response()->json([
                    'success' => true,
                    'message' => 'VehicleModel retrieved successfully',
                    'data' => $data,
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found',
                    'data' => null,
                ], 404);
            }
        }

        try {
            // Build base query with eager loading
            $query = VehicleModel::with(['category']);

            // Apply search filter
            if ($search) {
                $query->where('name', 'like', $search . '%');
            }

            // Apply category filter via relationship
            if ($category) {
                $query->whereHas('category', function ($q) use ($category) {
                    $q->where('name', $category);
                });
            }

            // Apply color filter (if many-to-many relationship exists)
            if ($color) {
                $query->whereHas('colors', function ($q) use ($color) {
                    $q->where('name', $color);
                });
                $query->with('colors');
            }

            // Apply status filter
            if ($status) {
                $query->where('status', 'like', $status . '%');
            }

            // Paginate results
            $VehicleModels = $query->paginate($paginate_count);

            // If no results
            if ($VehicleModels->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No VehicleModels found',
                    'data' => [],
                ], 404);
            }

            // Return paginated data
            return response()->json([
                'success' => true,
                'message' => 'VehicleModels retrieved successfully',
                'data' => $VehicleModels->items(),
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
        try {
            $validated = $this->validateRequest($request);

            $vehicleModel = new VehicleModel();

            // Reuse helper to populate fields
            HelperMethods::populateModelFields(
                $vehicleModel,
                $request,
                $validated,
                $this->typeOfFields,
                [
                    'imageFields' => $this->imageFields,
                    'textFields' => $this->textFields,
                    'numericFields' => $this->numericFields,
                ]
            );



            $vehicleModel->save();

            return $this->responseSuccess(
                $vehicleModel->load(['category']),
                'VehicleModel updated successfully',
                200
            );
        } catch (ValidationException $e) {
            DB::rollBack();

            Log::warning('Validation failed for VehicleModel store', [
                'request_data' => $request->all(),
                'errors' => $e->errors(),
            ]);

            return $this->responseError('Validation failed', $e->errors(), 422);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error storing VehicleModel: ' . $e->getMessage(), [
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
        try {
            $validated = $this->validateRequest($request);

            $vehicleModel = VehicleModel::findOrFail($id);

            // Reuse helper to populate fields
            HelperMethods::populateModelFields(
                $vehicleModel,
                $request,
                $validated,
                $this->typeOfFields,
                [
                    'imageFields' => $this->imageFields,
                    'textFields' => $this->textFields,
                    'numericFields' => $this->numericFields,
                ]
            );



            $vehicleModel->save();

            return $this->responseSuccess(
                $vehicleModel->load(['category']),
                'VehicleModel updated successfully',
                200
            );
        } catch (ValidationException $e) {
            Log::warning('Validation failed for VehicleModel update', [
                'vehicle_model_id' => $id,
                'request_data' => $request->all(),
                'errors' => $e->errors(),
            ]);

            return $this->responseError('Validation failed', $e->errors(), 422);
        } catch (\Exception $e) {
            Log::error('Error updating VehicleModel: ' . $e->getMessage(), [
                'vehicle_model_id' => $id,
                'request_data' => $request->all(),
                'error' => $e->getTraceAsString(),
            ]);

            return $this->responseError('Something went wrong', $e->getMessage(), 500);
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $item = VehicleModel::find($id);

        if ($item->inner_image && file_exists(public_path($item->inner_image))) {
            unlink(public_path($item->inner_image));
        }



        if (!$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        $item->delete();

        return response()->json(['message' => 'Item deleted successfully'], 200);
    }
}
