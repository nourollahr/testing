@extends('./../../layouts/app')

@section('content')
    <form method="post" action="{{ route('post.store') }}" class="row g-3 py-4 rounded shadow-sm container mx-auto bg-white">
        @csrf
        @if($errors->any())
            <ul class="alert alert-danger p-3 pl-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif
        <div class="col-12 mb-3">
            <label class="form-label">title</label>
            <input type="text" name="title" class="form-control" placeholder="title">
        </div>
        <div class="col-12 mb-3">
            <label class="form-label">description</label>
            <textarea class="form-control" name="description" placeholder="description" rows="3"></textarea>
        </div>
        <div class="col-6">
            <label class="form-label">tags</label>
            <select class="form-select w-100" name="tags" multiple>
                @foreach ($tags as $tag)
                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6">
            <label class="form-label">image</label>
            <input type="file" class="form-control" id="postImageInput">
            <input type="hidden" name="image">
            <img src="" id="uploadedImage" class="w-25 mx-auto" alt="">
        </div>
        <div class="col-12 d-flex my-3">
            <div class="form-check mr-3">
                <input class="form-check-input" type="checkbox">
                <label class="form-check-label">
                    Check me out
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" value="1">
                <label class="form-check-label">
                    Default radio
                </label>
            </div>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">save</button>
        </div>
    </form>
@endsection
