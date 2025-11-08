 @extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{modelNamePlural}}</h5>
                    <a href="{{ route('{{routePath}}.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Create New
                    </a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Search Form -->
                    <form method="GET" action="{{ route('{{routePath}}.index') }}" class="mb-3">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="{{searchPlaceholder}}" value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i> Search
                            </button>
                            @if(request('search'))
                                <a href="{{ route('{{routePath}}.index') }}" class="btn btn-outline-danger">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            @endif
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
{{tableHeaders}}
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(${{modelVariablePlural}} as ${{modelVariable}})
                                    <tr>
{{tableColumns}}
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('{{routePath}}.edit', ${{modelVariable}}) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('{{routePath}}.destroy', ${{modelVariable}}) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100" class="text-center">No records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ ${{modelVariablePlural}}->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif
</script>
@endpush
@endsection