@extends('layout.main')

@section('content')
    <div class="container">
        <h2>Employee ID Card Templates</h2>
        <a href="{{ route('templates.id-cards.create') }}" class="btn btn-primary">Create New Template</a>
        @if (session('success'))
            <div class="alert alert-success mt-3">
                {{ session('success') }}
            </div>
        @endif

        <style>
            /* Your custom CSS styling for the ID card template */

            .container {
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100vh;
                background-color: #e6ebe0;
            }

            .card {
                border: 1px solid #ccc;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                padding: 20px;
                max-width: 300px;
                margin: 0 auto;
            }

            .card-title {
                font-size: 20px;
                font-weight: bold;
                text-align: center;
                margin-bottom: 20px;
            }

            .card-preview {
                border: 1px solid #ddd;
                border-radius: 5px;
                padding: 10px;
                margin-bottom: 20px;
            }

            .card-preview img {
                max-width: 100%;
                height: auto;
            }

            .card-buttons {
                display: flex;
                justify-content: space-between;
            }

            .card-buttons button {
                flex: 1;
            }

            .alert {
                margin-top: 20px;
            }
        </style>

        <div class="row mt-4">
            @foreach ($templates as $template)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $template->name }}</h5>
                            <div class="card-preview">
                                {!! html_entity_decode($template->template_html) !!}
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('templates.id-cards.edit', $template->id) }}" class="btn btn-primary">Edit</a>
                                <form action="{{ route('templates.id-cards.delete', $template->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this template?')">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
