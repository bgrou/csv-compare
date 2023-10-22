@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">CSV File Comparison</h1>
        <div class="card">
            <div class="card-header">
                Upload CSV Files
            </div>
            <div class="card-body">
                {!! Form::open(['url' => '/upload_files', 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
                <div class="mb-3">
                    <label for="old_csv" class="form-label">Old CSV File</label>
                    {!! Form::file('old_csv', ['accept' => '.csv', 'required' => 'required', 'class' => 'form-control']) !!}
                </div>
                <div class="mb-3">
                    <label for="new_csv" class="form-label">New CSV File</label>
                    {!! Form::file('new_csv', ['accept' => '.csv', 'required' => 'required', 'class' => 'form-control']) !!}
                </div>
                <button type="submit" class="btn btn-primary">Compare CSVs</button>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
