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
     * Ensures values are proper types.
     *
     * @var array
     */
    protected $casts = [
        'header_nav_link' => 'boolean',
        'footer_nav_link' => 'boolean',
        'external' => 'boolean'
    ];

    /**
     * Hook into the `creating` event to set default attribute values
     */
    public static function boot()
    {
        parent::boot();

        Page::creating(function($page) {
            $page->page_title = $page->nav_title;
            $page->header = $page->nav_title;
            $page->header_nav_link = true;
            $page->footer_nav_link = false;
            $page->external = false;

            // Lowercase, strip all symbols, then replace space with dash
            $page->url = strtolower(
                str_replace(' ', '-',
                    preg_replace('/[^\p{L}\p{N}\s]/u', '', $page->nav_title)
                )
            );
        });
    }

    /**
     * Association with PageContent
     */
    public function content()
    {
        return $this->belongsTo(PageContent::class);
    }

}
