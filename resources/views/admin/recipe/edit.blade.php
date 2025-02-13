@extends('admin.layouts.master')
@section('content')
<h1>Edit Recipe</h1>
<form action="{{ route('recipes.update', $recipe->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <label>Title:</label>
    <input type="text" name="title" class="form-control" value="{{ $recipe->title }}" required>
    <label>Description:</label>
    <textarea name="description" class="form-control">{{ $recipe->description }}</textarea>
    <label>Ingredients:</label>
    <textarea name="ingredients" class="form-control" required>{{ $recipe->ingredients }}</textarea>
    <label>Instructions:</label>
    <textarea name="instructions" class="form-control" required>{{ $recipe->instructions }}</textarea>
    <label>Preparation Time (minutes):</label>
    <input type="number" name="preparation_time" class="form-control" value="{{ $recipe->preparation_time }}">
    <label>Cooking Time (minutes):</label>
    <input type="number" name="cooking_time" class="form-control" value="{{ $recipe->cooking_time }}">
    <label>Total Time (minutes):</label>
    <input type="number" name="total_time" class="form-control" value="{{ $recipe->total_time }}">
    <label>Servings:</label>
    <input type="number" name="servings" class="form-control" value="{{ $recipe->servings }}">
    <label>Difficulty Level:</label>
    <input type="text" name="difficulty_level" class="form-control" value="{{ $recipe->difficulty_level }}">
    <label>Category:</label>
    <input type="text" name="category" class="form-control" value="{{ $recipe->category }}">
    <label>Dietary Preferences:</label>
    <input type="text" name="dietary_preferences" class="form-control" value="{{ $recipe->dietary_preferences }}">
    <label>Author Name:</label>
    <input type="text" name="author_name" class="form-control" value="{{ $recipe->author_name }}">
    <label>Publication Date:</label>
    <input type="date" name="publication_date" class="form-control" value="{{ $recipe->publication_date }}">
    <label>Image:</label>
    <input type="file" name="image" class="form-control">
    <button type="submit" class="btn btn-success">Update</button>
</form>
@endsection