@extends('layouts.app')

@section('content')
    <div class="container mt-5" style="font-size: 12px">
        <h1 class="mb-4">CSV File Comparison</h1>
        <div class="card">
            <div class="card-header" style="font-size: 16px">
                Upload CSV Files
            </div>
            <div class="card-body">
                {!! Form::open(['url' => '/upload_files', 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
                <div class="mb-3">
                    <label for="old_csv" style="font-size: 14px" class="form-label">Old CSV File</label>
                    {!! Form::file('old_csv', ['accept' => '.csv', 'required' => 'required', 'class' => 'form-control']) !!}
                </div>
                <div class="mb-3">
                    <label for="new_csv" style="font-size: 14px" class="form-label">New CSV File</label>
                    {!! Form::file('new_csv', ['accept' => '.csv', 'required' => 'required', 'class' => 'form-control']) !!}
                </div>
                <button type="submit" class="btn btn-primary">Compare CSVs</button>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
