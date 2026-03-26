@extends('install.layout', ['step' => 2])
@section('content')
<div class="card-body p-4">
    <h5 class="mb-3"><i class="fas fa-clipboard-check text-primary me-2"></i>Server Requirements</h5>
    <table class="table table-sm">
        @foreach($checks as $name => $passed)
        <tr>
            <td>{{ $name }}</td>
            <td class="text-end">
                @if($passed)
                    <span class="text-success"><i class="fas fa-check-circle"></i> Pass</span>
                @else
                    <span class="text-danger"><i class="fas fa-times-circle"></i> Fail</span>
                @endif
            </td>
        </tr>
        @endforeach
    </table>
</div>
<div class="card-footer d-flex justify-content-between">
    <a href="{{ route('install.welcome') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
    @if($allPassed)
        <a href="{{ route('install.database') }}" class="btn btn-primary">Next <i class="fas fa-arrow-right ms-1"></i></a>
    @else
        <button class="btn btn-secondary" disabled>Fix requirements first</button>
    @endif
</div>
@endsection
