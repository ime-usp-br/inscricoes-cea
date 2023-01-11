<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepositReceiptRequest;
use App\Http\Requests\UpdateDepositReceiptRequest;
use App\Models\DepositReceipt;
use Illuminate\Support\Facades\Storage;

class DepositReceiptController extends Controller
{
    public function download(DepositReceipt $receipt)
    {
        $tmp = explode(".",$receipt->path);

        $ext = !str_contains($receipt->name, "." . end($tmp)) ? "." . end($tmp) : "";

        return Storage::download($receipt->path, $receipt->name . $ext);
    }
}
