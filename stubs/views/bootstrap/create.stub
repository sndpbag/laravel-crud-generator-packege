 @extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Create {{modelName}}</h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('{{routePath}}.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

{{formFields}}

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('{{routePath}}.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection