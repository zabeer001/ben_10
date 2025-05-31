<?php

namespace App\Http\Controllers;

use App\Models\CustomerInfo;
use App\Models\ModelColorWiseImage;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Helpers\HelperMethods;

class OrderController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api')->only(['index']);
    }

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
            'uniq_id' => 'nullable|string'
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
                $order = Order::with(['vehicleModel', 'theme', 'customerInfo', 'colors', 'additionalOptions'])->find($id);
                // Get vehicle model ID
                $vehiclemodel_id = $order->vehicleModel?->id;

                // Get color IDs (assuming 'colors' is a relationship returning a collection)
                $color_ids = $order->colors->pluck('id')->toArray(); // This gives you an array of color IDs

                // Get specific colors if they exist
                $color_id_1 = $color_ids[0] ?? null;
                $color_id_2 = $color_ids[1] ?? null;

                $model_color_wise_image = ModelColorWiseImage::with(['vehicleModel', 'color1', 'color2'])
                    ->where('vehiclemodel_id', $vehiclemodel_id)
                    ->where('color_1_id', $color_id_1)
                    ->where('color_2_id', $color_id_2)
                    ->get();
                if ($order) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Order retrieved successfully',
                        'data' => $order,$model_color_wise_image,
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

            $uniqId = HelperMethods::generateUniqueId();

            // Return the length of that unique ID string (should be 40)


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
            $data->uniq_id = $uniqId;
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
    public function show($uniq_id)
    {
        $order = Order::with(['vehicleModel', 'theme', 'customerInfo', 'colors', 'additionalOptions'])
            ->where('uniq_id', $uniq_id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
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
        try {
            $order = Order::findOrFail($id);

            // Delete only the pivot records in additional_option_order
            $order->additionalOptions()->detach();

            // Delete the order
            $order->delete();

            return response()->json(['message' => 'Order deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete order.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string', // modify as needed
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully.',
            'data' => $order,
        ]);
    }





}
