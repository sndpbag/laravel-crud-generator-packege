@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit {{modelName}}</h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('{{routePath}}.update', ${{modelVariable}}) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

{{formFields}}

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('{{routePath}}.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection