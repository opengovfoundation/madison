<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Page;

use GrahamCampbell\Markdown\Facades\Markdown;

class PageContent extends Model
{
    protected $table = 'page_contents';

    /**
     * Mass assignable attributes
     *
     * @var array
     */
    protected $fillable = ['page_id', 'content'];

    /**
     * Associate with Page
     */
    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Return HTML version of page content
     */
    public function html()
    {
        return Markdown::convertToHtml($this->content);
    }

    /**
     * Return Markdown version of page content
     */
    public function markdown()
    {
        return $this->content;
    }
}
