<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadImageController extends Controller
{
    public function upload(Request $request)
    {
        $image = $request->file('image');

        $image->move(public_path('/upload/'), $image->hashName());

        return ['url' => "/upload/{$image->hashName()}"];
    }
}
