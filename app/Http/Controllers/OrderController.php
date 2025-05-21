<?php

namespace App\Http\Controllers;

use App\Models\CustomerInfo;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    protected $typeOfFields = ['textFields'];



    protected $textFields = [
        'vehicle_model_id',
        'theme_id',
        'base_price',
        'total_price',
    ];


    protected $customerInfoFields = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'postal_code',
    ];

    protected function validateOrder(Request $request)
    {
        return $request->validate([
            'vehicle_model_id' => 'nullable',
            'theme_id' => 'nullable',
            'base_price' => 'required|numeric',
            'total_price' => 'required|numeric',
            'color_id' => 'nullable|array',
            'additional_option_id' => 'nullable|array',
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'email' => 'nullable|string',
            'phone' => 'nullable|string',
            'postal_code' => 'nullable|string',
        ]);
    }
    protected function insertCustomerInfo($request)
    {
        // Check if a customer with the same email already exists
        $existingCustomer = CustomerInfo::where('email', $request->email)->first();

        if ($existingCustomer) {
            // Return existing customer
            return $existingCustomer;
        }

        // Create a new customer if not found
        $customer = new CustomerInfo();
        foreach ($this->customerInfoFields as $field) {
            if ($request->has($field)) {
                $customer->$field = $request->$field;
            }
        }
        $customer->save();
        return $customer;
    }



    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|integer|min:1',
            'paginate_count' => 'nullable|integer|min:1|max:100',
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:pending,completed,cancelled',
        ]);

        $paginate_count = $validated['paginate_count'] ?? 10;
        $id = $validated['id'] ?? null;
        $search = $validated['search'] ?? null;
        $status = $validated['status'] ?? null;

        try {
            if ($id) {
                $order = Order::with(['vehicleModel', 'theme', 'customerInfo'])->find($id);
                if ($order) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Order retrieved successfully',
                        'data' => $order,
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Order not found',
                        'data' => null,
                    ], 404);
                }
            }

            // Start with a query builder without eager loading
            $query = Order::query();

            // Apply search filter
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', '%' . $search . '%')
                        ->orWhereHas('customerInfo', function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%');
                        });
                });
            }

            // Apply status filter
            if ($status) {
                $query->where('status', $status);
            }

            // Paginate the result
            $orders = $query->paginate($paginate_count);

            // Check if any data was returned
            if ($orders->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No orders found',
                    'data' => [],
                ], 404);
            }

            // Return with pagination meta
            return response()->json([
                'success' => true,
                'message' => 'Orders retrieved successfully',
                'data' => $orders->items(),
                'current_page' => $orders->currentPage(),
                'total_pages' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Show the form for creating a new resource.
     */

    public function store(Request $request)
    {
        try {
            $validated = $this->validateOrder($request);

            $customer = $this->insertCustomerInfo($request);


            $data = new Order;

            foreach ($this->typeOfFields as $fieldType) {
                foreach ($this->{$fieldType} as $field) {
                    switch ($fieldType) {
                        case 'textFields':
                            if (isset($validated[$field])) {
                                $data->$field = $validated[$field];
                            }
                            break;
                    }
                }
            }
            // return 'ok';
//   return $validated['color_id'];

            $data->customer_info_id = $customer->id;

            $data->save();

            $data->colors()->sync($validated['color_id']);

            $data->additionalOptions()->sync($validated['additional_option_id']);


            return response()->json(['data' => $data, 'message' => 'Order created successfully', 'customer_info' => $customer], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // GET /orders/{id}
    public function show($id)
    {
        $order = Order::with(['vehicleModel', 'theme', 'customerInfo'])->findOrFail($id);
        return response()->json(['data' => $order]);
    }

    // PUT/PATCH /orders/{id}
    public function update(Request $request, $id)
    {
        $validated = $this->validateOrder($request);

        $order = Order::findOrFail($id);
        $order->update($validated);

        return response()->json(['data' => $order, 'message' => 'Order updated successfully']);
    }

    // DELETE /orders/{id}
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }




}
