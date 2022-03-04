<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\NewsCategory;
use App\Models\NewsSubCategory;
use Carbon\Carbon;

class News extends Model
{
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $table = 'news';
    protected $casts = [
        'created_at' => 'datetime:l d-m-Y',
    ];
    protected $fillable = [
        'category_id',
        'sub_category_id',
        'title',
        'description',
        'picture_xl',
        'picture_sm',
        'picture_xs',
        'user_id',
        'city',
        'popular',
        'breaking',
        'trending',
        'slug'
    ];

    protected $columns = [
        'category_id',
        'sub_category_id',
        'title',
        'description',
        'picture_xl',
        'picture_sm',
        'picture_xs',
        'user_id',
        'city',
        'popular',
        'breaking',
        'trending',
        'slug'
    ];

    // protected $columns = ['id','description']; // add all columns from you table

    public function scopeExclude($query, $value = []) 
    {
        return $query->select(array_diff($this->columns, (array) $value));
    }

    public function main_category()
    {
        return $this->hasOne(NewsCategory::class,'id', 'category_id');
    }

    public function sub_category()
    {
        return $this->hasOne(NewsSubCategory::class,'id', 'sub_category_id');
    }

    // public function getCreatedAtAttribute($date)
    // {
    //     $this->attributes['date'] = (new Carbon($value))->format('d/m/y');
    //     return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d');
    // }
    
    // public function getUpdatedAtAttribute($date)
    // {
    //     return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d');
    // }

}
