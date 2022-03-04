<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ENewsPaperPages extends Model
{
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $table = 'e_news_paper_pages';
    protected $fillable = [
        'e_news_paper_id',
        'page_no',
        'image'
    ];
}
