<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();


        return response()->json($categories);
    }

    public function destroy($id)
    {
        $part = Category::findOrFail($id);
        $part->delete(); // this does a soft delete

        return response()->json(['message' => 'Part deleted (soft).']);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update($validatedData);

        return response()->json(['message' => 'Category updated successfully.', 'category' => $category]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::create($validatedData);

        return response()->json(['message' => 'Category created successfully.', 'category' => $category], 201);
    }
}
