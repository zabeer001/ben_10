<?php

namespace App\Http\Controllers;

use App\Models\AdditionalOption;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FrontendQueryController extends Controller
{
    public function frontendModelsCategory()
    {
        try {
            // $data = DB::table('categories as c')
            //     ->select([
            //         'c.id as category_id',
            //         'c.name as category_name',
            //         DB::raw("GROUP_CONCAT(
            //             JSON_OBJECT(
            //                 'id', vm.id,
            //                 'name', vm.name
            //             )
            //             SEPARATOR '|'
            //         ) as models")
            //     ])
            //     ->join('vehicle_models as vm', 'vm.category_id', '=', 'c.id')
            //     ->groupBy('c.id', 'c.name')
            //     ->get();
            $data = Category::with(['vehicleModels:id,category_id,name'])->get();

            return $data;

            // Format the data: decode JSON models into arrays
            // $formatted = $data->map(function ($item) {
            //     // Split by custom separator and filter out empty strings
            //     $modelStrings = array_filter(explode('|', $item->models));
            //     $models = array_map(function ($modelString) {
            //         $decoded = json_decode($modelString, true);
            //         return is_array($decoded) ? $decoded : null;
            //     }, $modelStrings);

            //     return [
            //         'category_id' => $item->category_id,
            //         'category_name' => $item->category_name,
            //         'models' => array_filter($models), // Remove nulls
            //     ];
            // });

            return response()->json([
                'status' => 'success',
                'message' => 'Categories and models fetched successfully',
                'data' => $formatted,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching category-wise models: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => request()->all(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch categories and models',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }


    public function frontendAdditionalOptions(Request $request)
    {
        $validated = $request->validate([
            'type' => 'nullable|string', // Adjust with your actual type values
        ]);

        $type = $validated['type'] ?? null;

        $query = AdditionalOption::query();

        // Apply type filter if provided
        if ($type) {
             $query->where('type', 'like', $type . '%');
        }

        $additionalOptions = $query->get(); // Fixed variable name

        // Group by category_name
        $categoryWiseAdditionalOptions = $additionalOptions->groupBy('category_name');

        return response()->json([
            'success' => true,
            'data' => $categoryWiseAdditionalOptions
        ]);
    }


}
