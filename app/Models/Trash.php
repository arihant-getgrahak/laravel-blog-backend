<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trash extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'blog_id',
    ];

    protected function casts()
    {
        return [
            "created_at" => "datetime:Y-m-d",
            "updated_at" => "datetime:Y-m-d",
        ];
    }
}
