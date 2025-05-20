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
    ];

    protected array $textFields = [
        'name',
        'flooring_name',
        'cabinetry_1_name',
        'table_top_1_name',
        'seating_1_name',
    ];
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        // Validate the incoming request
        $validated = $this->validateRequest($request);

        try {
            // Handle all image uploads
            $imagePath = $request->hasFile('image') ? HelperMethods::uploadImage($request->file('image')) : null;
            $flooringImagePath = $request->hasFile('flooring_image') ? HelperMethods::uploadImage($request->file('flooring_image')) : null;
            $cabinetryImagePath = $request->hasFile('cabinetry_1_image') ? HelperMethods::uploadImage($request->file('cabinetry_1_image')) : null;
            $tableTopImagePath = $request->hasFile('table_top_1_image') ? HelperMethods::uploadImage($request->file('table_top_1_image')) : null;
            $seatingImagePath = $request->hasFile('seating_1_image') ? HelperMethods::uploadImage($request->file('seating_1_image')) : null;

            // Create a new theme
            $theme = Theme::create([
                'name' => $validated['name'],
                'image' => $imagePath,

                'flooring_name' => $validated['flooring_name'],
                'flooring_image' => $flooringImagePath,

                'cabinetry_1_name' => $validated['cabinetry_1_name'],
                'cabinetry_1_image' => $cabinetryImagePath,

                'table_top_1_name' => $validated['table_top_1_name'],
                'table_top_1_image' => $tableTopImagePath,

                'seating_1_name' => $validated['seating_1_name'],
                'seating_1_image' => $seatingImagePath,
            ]);

            return $this->responseSuccess(
                $theme,
                'Theme created successfully',
                201
            );
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
        //
    }

    private function validateRequest(Request $request)
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:10240',

            'flooring_name' => 'required|string|max:255',
            'flooring_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:10240',

            'cabinetry_1_name' => 'required|string|max:255',
            'cabinetry_1_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:10240',

            'table_top_1_name' => 'required|string|max:255',
            'table_top_1_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:10240',

            'seating_1_name' => 'required|string|max:255',
            'seating_1_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ]);
    }
}
