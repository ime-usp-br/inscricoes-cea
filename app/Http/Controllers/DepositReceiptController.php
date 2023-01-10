<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepositReceiptRequest;
use App\Http\Requests\UpdateDepositReceiptRequest;
use App\Models\DepositReceipt;
use Illuminate\Support\Facades\Storage;

class DepositReceiptController extends Controller
{
    public function download(Attachment $attachment)
    {
        $tmp = explode(".",$attachment->path);

        $ext = !str_contains($attachment->name, "." . end($tmp)) ? "." . end($tmp) : "";

        return Storage::download($attachment->path, $attachment->name . $ext);
    }
}
