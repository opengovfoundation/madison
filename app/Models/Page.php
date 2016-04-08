<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PageContent;

class Page extends Model
{
    /**
     * Mass assignable attributes
     *
     * @var array
     */
    protected $fillable = [
        'url',
        'nav_title',
        'page_title',
        'header',
        'header_nav_link',
        'footer_nav_link',
        'external'
    ];

    /**
     * Association with PageContent
     */
    public function content()
    {
        return $this->belongsTo(PageContent::class);
    }
}
