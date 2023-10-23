@extends('layouts.app')

@section('content')
    <div class="d-flex p-2">
        <h2 class="mb-4">CSV Files Comparison Result</h2>
        <a href="/">
            <button class="btn btn-primary ml-3">Submit new files</button>
        </a>
    </div>
    <div class="mb-4">
        <strong>New Records:</strong> {{ $csvComparison['new_count'] }} <br>
        <strong>Altered Records:</strong> {{ $csvComparison['alter_count'] }} <br>
        <strong>Equal Records:</strong> {{ $csvComparison['equal_count'] }}
    </div>

    @if(isset($csvComparison) && count($csvComparison) > 0)
        <table class="table full-width table-bordered">
            <thead class="thead-light">
            <tr>
                <th>Indexes</th>
                @foreach($csvComparison['records'][0] as $key => $value)
                    <th>{{$value}}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach(array_slice($csvComparison['records'], 1) as $record)
                <tr class={{($record['type'] == 'new') ? 'new' : '' }}>
                    <td>{{$record['old_index'] . ':' . $record['new_index'] }}</td>
                    @if(!empty($record['record']))
                        @foreach($record['record'] as $key => $value)
                            <td
                                @if(isset($record['diffs'][$key]))
                                    title="{{ "Old Value: " . $value }}"
                                style="background-color: #a7c7e7"
                                @endif
                            >
                                {{ isset($record['diffs'][$key]) ? $record['diffs'][$key] : $value }}
                            </td>
                        @endforeach
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <p>No differences found.</p>
    @endif
@endsection
