@extends('layout.main')

@section('content')
    <div class="container">
        <h2>Edit Employee ID Card Template</h2>
        <form action="{{ route('templates.id-cards.update', $template->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name">Template Name</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ $template->name }}" required>
            </div>
            <div class="form-group">
                <label for="template_html">Template HTML</label>
                <textarea name="template_html" id="template_html" class="form-control" required>{{ $template->template_html }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Template</button>
        </form>
    </div>
@endsection
