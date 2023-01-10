<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttachmentRequest;
use App\Http\Requests\UpdateAttachmentRequest;
use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function download(Attachment $attachment)
    {
        $tmp = explode(".",$attachment->path);

        $ext = !str_contains($attachment->name, "." . end($tmp)) ? "." . end($tmp) : "";

        return Storage::download($attachment->path, $attachment->name . $ext);
    }
}
