@extends('admin.layouts.master')
@section('content')
<h1>Recipes</h1>
<a href="{{ route('recipes.create') }}" class="btn btn-primary">Add Recipe</a>
<table class="table">
    <thead>
        <tr>
            <th>Title</th>
            <th>Category</th>
            <th>Servings</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($recipes as $recipe)
            <tr>
                <td>{{ $recipe->title }}</td>
                <td>{{ $recipe->category }}</td>
                <td>{{ $recipe->servings }}</td>
                <td>
                    <a href="{{ route('recipes.edit', $recipe->id) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('recipes.destroy', $recipe->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection