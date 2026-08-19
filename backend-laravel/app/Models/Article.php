<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'title','slug','excerpt','body','image_url','category','author_name',
        'published_at','is_breaking','is_featured','views',
        'provider','provider_id','source_name','source_url','source_domain',
        'locale','source_published_at','content_policy'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'source_published_at' => 'datetime',
        'is_breaking' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected function isPublished(): Attribute
    {
        return Attribute::get(fn () => !is_null($this->published_at));
    }
}
