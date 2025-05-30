<?php

namespace App\Http\Controllers;

use App\Models\AdditionalOption;
use Illuminate\Http\Request;

class AdditionalOptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function __construct()
    {
        // Apply JWT authentication middleware only to store, update, and destroy methods
        $this->middleware('auth:api')->only(['store', 'update', 'destroy', 'statusUpdate']);
    }
public function index(Request $request)
{
    $validated = $request->validate([
        'paginate_count' => 'nullable|integer|min:1',
        'search' => 'nullable|string|max:255',
        'category' => 'nullable|string|exists:categories,name',
        'type' => 'nullable|string|max:255',
    ]);

    $paginate_count = $validated['paginate_count'] ?? 10;
    $search = $validated['search'] ?? null;
    $category = $validated['category'] ?? null;
    $type = $validated['type'] ?? null;

    try {
        // ✅ Use query builder
        $query = AdditionalOption::query();

        if ($search) {
            $query->where('name', 'like', $search . '%');
        }

        if ($category) {
            $query->where('category_name', 'like', $category . '%');
        }

        if ($type) {
            $query->where('type', 'like', $type . '%');
        }

        $AdditionalOptions = $query->paginate($paginate_count);

        if ($AdditionalOptions->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No AdditionalOptions found',
                'data' => [],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'AdditionalOptions retrieved successfully',
            'data' => $AdditionalOptions,
            'current_page' => $AdditionalOptions->currentPage(),
            'total_pages' => $AdditionalOptions->lastPage(),
            'per_page' => $AdditionalOptions->perPage(),
            'total' => $AdditionalOptions->total(),
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch AdditionalOptions.',
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
        // return 'ok';
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric',
                'vehicle_model_id' => 'nullable',
                'category_name' => 'required|string|max:255',
                'type' => 'required|string|max:255',
            ]);

            $option = AdditionalOption::create($validated);

            return response()->json($option, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation failed
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Other errors
            return response()->json([
                'message' => 'Failed to create option',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AdditionalOption $additionalOption)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AdditionalOption $additionalOption)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric',
                'vehicle_model_id' => 'nullable',
                'category_name' => 'required|string|max:255',
                'type' => 'required|string|max:255',
            ]);

            $option = AdditionalOption::findOrFail($id);
            $option->update($validated);

            return response()->json($option, 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update option',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
   public function destroy($id)
    {
        try {
            $AdditionalOption = AdditionalOption::findOrFail($id);

        

            // Delete the order
            $AdditionalOption->delete();

            return response()->json(['message' => 'data deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function allTypes()
    {
    // return 0 ;
        $category_name = AdditionalOption::distinct()->pluck('category_name');
        return response()->json([
            'success' => true,
            'data' => $category_name
        ]);
    }

}
