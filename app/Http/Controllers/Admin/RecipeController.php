<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Recipe;

class RecipeController extends Controller
{
    public function index()
    {
        $recipes = Recipe::all();
        return view('admin.recipes.index', compact('recipes'));
    }

    public function create()
    {
        return view('admin.recipes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'ingredients' => 'required',
            'instructions' => 'required',
            'image' => 'nullable|image',
            'preparation_time' => 'nullable|integer',
            'cooking_time' => 'nullable|integer',
            'total_time' => 'nullable|integer',
            'servings' => 'nullable|integer',
            'difficulty_level' => 'nullable|string',
            'category' => 'nullable|string',
            'dietary_preferences' => 'nullable|string',
            'author_name' => 'nullable|string',
            'publication_date' => 'nullable|date',
            // 'rating' => 'nullable|numeric',
            // 'comments' => 'nullable|string',
        ]);

        Recipe::create($request->all());
        return redirect()->route('recipes.index')->with('success', 'Recipe created successfully.');
    }

    public function edit(Recipe $recipe)
    {
        return view('admin.recipes.edit', compact('recipe'));
    }

    public function update(Request $request, Recipe $recipe)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'ingredients' => 'required',
            'instructions' => 'required',
        ]);

        $recipe->update($request->all());
        return redirect()->route('recipes.index')->with('success', 'Recipe updated successfully.');
    }

    public function destroy(Recipe $recipe)
    {
        $recipe->delete();
        return redirect()->route('recipes.index')->with('success', 'Recipe deleted successfully.');
    }
}
