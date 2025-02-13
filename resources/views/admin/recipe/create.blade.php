@extends('admin.layouts.master')
@section('content')
<h1>Add New Recipe</h1>
<form action="{{ route('recipes.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <label>Title:</label>
    <input type="text" name="title" class="form-control" required>
    <label>Description:</label>
    <textarea name="description" class="form-control"></textarea>
    <label>Ingredients:</label>
    <textarea name="ingredients" class="form-control" required></textarea>
    <label>Instructions:</label>
    <textarea name="instructions" class="form-control" required></textarea>
    <label>Preparation Time (minutes):</label>
    <input type="number" name="preparation_time" class="form-control">
    <label>Cooking Time (minutes):</label>
    <input type="number" name="cooking_time" class="form-control">
    <label>Total Time (minutes):</label>
    <input type="number" name="total_time" class="form-control">
    <label>Servings:</label>
    <input type="number" name="servings" class="form-control">
    <label>Difficulty Level:</label>
    <input type="text" name="difficulty_level" class="form-control">
    <label>Category:</label>
    <input type="text" name="category" class="form-control">
    <label>Dietary Preferences:</label>
    <input type="text" name="dietary_preferences" class="form-control">
    <label>Author Name:</label>
    <input type="text" name="author_name" class="form-control">
    <label>Publication Date:</label>
    <input type="date" name="publication_date" class="form-control">
    <label>Image:</label>
    <input type="file" name="image" class="form-control">
    <button type="submit" class="btn btn-success">Save</button>
</form>
@endsection