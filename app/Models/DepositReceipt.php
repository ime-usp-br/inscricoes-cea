<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositReceipt extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = [
        'name',
        'path',
        'applicationID',
        'link',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class, "applicationID");
    }
}
