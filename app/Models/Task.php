<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'task_code',
        'title',
        'description',
        'document',
        'status',
        'deadline',
        'users_code',
        'is_delete',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'users_code', 'users_code');
    }
}
