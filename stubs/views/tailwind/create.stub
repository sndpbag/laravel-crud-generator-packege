 @extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md">
        <div class="p-6 border-b">
            <h2 class="text-2xl font-bold text-gray-800">Create {{modelName}}</h2>
        </div>

        <div class="p-6">
            <form action="{{ route('{{routePath}}.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

{{formFields}}

                <div class="flex justify-between mt-6">
                    <a href="{{ route('{{routePath}}.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">
                        Back
                    </a>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg">
                        Create
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection