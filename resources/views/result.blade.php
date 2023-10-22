<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>CSV Compare</title>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            padding: 20px;
            font-size: 12px;
        }

        .new {
            background-color: #ccffd9;
        }

        .alter {
            background-color: #a7c7e7;
        }

        table {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="d-flex p-2">
        <h2 class="mb-4">CSV Files Comparison Result</h2>
        <a href="/"><p class="mb-4 ml-5">Submit New Files</p></a>
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
                                class="alter"
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
</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
