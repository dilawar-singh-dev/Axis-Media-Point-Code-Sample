<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsCronJobs extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $table = 'news_cron_jobs';
    protected $fillable = [
        'title',
        'status',
        'exception'
    ];
}
