<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\News;
use App\Models\NewsCategory;
use Carbon\Carbon;

class NewsSubCategory extends Model
{
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $table = 'news_sub_category';
    protected $fillable = [
        'category_id',
        'name'
    ];
    protected $casts = [
        'created_at' => 'datetime:l d-m-Y',
    ];
    public function sub_category_news()
    {
        return $this->hasMany(News::class, 'sub_category_id');
    }

    public function category()
    {
        return $this->hasOne(NewsCategory::class,'id', 'category_id');
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
