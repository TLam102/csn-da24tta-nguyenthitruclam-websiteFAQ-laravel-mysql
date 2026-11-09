<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TheTag extends Model
{
    protected $table = 'the_tag';
    protected $primaryKey = 'the_id';
    public $timestamps = false;
    protected $fillable = ['ten_the', 'slug'];

    public function faqs()
    {
        return $this->belongsToMany(Faq::class, 'faq_the_tag', 'the_id', 'faq_id', 'the_id', 'faq_id');
    }
}
