<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AxisTv extends Model
{
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $table = 'axis_tv';
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'yt_video',
        'type'
    ];
}
