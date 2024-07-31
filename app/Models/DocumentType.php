<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    use HasFactory;

    use HasFactory;

    public $timestamps = false;

    protected $table = 'document_types';

    protected $fillable = [
        'name',
        'description',
    ];
}
