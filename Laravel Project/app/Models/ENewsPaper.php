<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ENewsPaper extends Model
{
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $table = 'e_news_paper';
    protected $fillable = [
        'date'
    ];

    public function papers()
    {
        return $this->hasMany(ENewsPaperPages::class, 'e_news_paper_id');
    }

}
