<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessDocument extends Model
{
    use HasFactory;

    protected $table = 'business_documents';

    protected $fillable = [
        'document_type_id',
        'name',
        'business_id',
        'path',
        'is_verified',
    ];
}
