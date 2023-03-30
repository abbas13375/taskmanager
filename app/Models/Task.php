<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    const STORAGE_DIR_FILE = 'public';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'completed',
        'file'
    ];
}
