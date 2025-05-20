<?php

namespace App\Http\Controllers;

use App\Models\CustomerInfo;
use Illuminate\Http\Request;

class CustomerInfoController extends Controller
{
    public function __construct()
    {
        // Apply JWT authentication middleware only to store, update, and destroy methods
        $this->middleware('auth:api')->only(['update', 'destroy', 'statusUpdate']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Validate query parameters
        $validated = $request->validate([
            'paginate_count' => 'nullable|integer|min:1',
            'search' => 'nullable|string|max:255',
        ]);

        $paginate_count = $validated['paginate_count'] ?? 10;
        $search = $validated['search'] ?? null;

        try {
            // Start building the query
            $query = CustomerInfo::orderBy('id', 'desc');

            // Apply search filter for email or phone number only
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('email', 'like', $search . '%')
                        ->orWhere('phone_number', 'like', $search . '%');
                });
            }

            // Paginate results
            $data = $query->paginate($paginate_count);

            // Check if any data was returned
            if ($data->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No CustomerInfo found',
                    'data' => [],
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'CustomerInfo retrieved successfully',
                'data' => $data,
                'current_page' => $data->currentPage(),
                'total_pages' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch CustomerInfo.',
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
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customer_infos,email',
            'phone' => 'required|string|max:20',
            'postal_code' => 'required|string|max:20',
        ]);

        $customer = CustomerInfo::create($validated);

        return response()->json($customer, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $customer = CustomerInfo::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }
        return response()->json($customer);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomerInfo $customerInfo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $customer = CustomerInfo::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $validated = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:customer_infos,email,' . $id,
            'phone' => 'sometimes|required|string|max:20',
            'postal_code' => 'sometimes|required|string|max:20',
        ]);

        $customer->update($validated);

        return response()->json($customer);
    }

    // Delete customer info
    public function destroy($id)
    {
        $customer = CustomerInfo::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $customer->delete();

        return response()->json(['message' => 'Customer deleted successfully']);
    }
}
