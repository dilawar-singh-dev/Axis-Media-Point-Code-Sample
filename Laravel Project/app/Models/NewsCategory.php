<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\News;
use App\Models\NewsSubCategory;
use Carbon\Carbon;

class NewsCategory extends Model
{
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $table = 'news_category';
    protected $casts = [
        'created_at' => 'datetime:l d-m-Y',
    ];
    protected $fillable = [
        'name'
    ];

    public function category_news()
    {
        return $this->hasMany(News::class, 'category_id');
    }

    public function sub_category()
    {
        return $this->hasMany(NewsSubCategory::class, 'category_id');
    }
    // public function getCreatedAtAttribute($date)
    // {
    //     return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d');
    // }
    
    // public function getUpdatedAtAttribute($date)
    // {
    //     return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d');
    // }
}
