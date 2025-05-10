<?php

namespace App\Http\Controllers;

use App\Models\Part;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PartController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'date_acquired' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle file upload to public storage
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('parts', 'public'); // Now stores in storage/app/public/parts
            $validated['image_path'] = $path; // This will be a string like "parts/filename.jpg"
        }

        $validated['user_id'] = Auth::id();

        $part = Part::create($validated);

        return response()->json([
            'success' => true,
            'data' => $part,
            'message' => 'Part created successfully'
        ], 201);
    }
    public function update(Request $request, Part $part)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'category_id' => 'sometimes|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'quantity' => 'sometimes|integer|min:0',
            'price' => 'sometimes|numeric|min:0',
            'date_acquired' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {

            if ($part->image_path) {
                Storage::delete($part->image_path);
            }

            $path = $request->file('image')->store('parts', 'public');
            $validated['image_path'] = $path;
        }

        $part->update($validated);

        return response()->json([
            'success' => true,
            'data' => $part,
            'message' => 'Part updated successfully'
        ]);
    }

    // Add this method to retrieve the image
    public function getImage(Part $part)
    {
        if (!$part->image_path || !Storage::exists($part->image_path)) {
            abort(404);
        }

        return response()->file(storage_path('app/' . $part->image_path));
    }


    public function index()
    {
        $parts = Part::with(['category', 'supplier'])->get();

        $parts->transform(function ($part) {
            if ($part->image_path) {
                $part->image_url = asset('storage/' . $part->image_path);
            } else {
                $part->image_url = null;
            }
            return $part;
        });

        return response()->json([
            'success' => true,
            'data' => $parts
        ]);
    }
    public function destroy($id)
    {
        $part = Part::find($id);

        if ($part) {
            $part->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Part deleted successfully'
        ]);
    }
}
