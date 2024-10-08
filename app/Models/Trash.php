<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trash extends Model
{
    use HasFactory;

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

    public function blogs()
    {
        return $this->belongsTo(Blog::class, 'blog_id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
