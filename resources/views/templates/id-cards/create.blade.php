@extends('layout.main')

@section('content')
    <div class="container">
        <h2>Create Employee ID Card Template</h2>
        <form action="{{ route('templates.id-cards.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Template Name</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="template_html">Template HTML</label>
                <textarea name="template_html" id="template_html" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Create Template</button>
        </form>
    </div>
@endsection
