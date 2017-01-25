<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
            if (!isset($page->page_title)) $page->page_title = $page->nav_title;
            if (!isset($page->header)) $page->header = $page->nav_title;
            if (!isset($page->header_nav_link)) $page->header_nav_link = true;
            if (!isset($page->footer_nav_link)) $page->footer_nav_link = false;
            if (!isset($page->external)) $page->external = false;

            // Lowercase, strip all symbols, then replace space with dash
            if (!isset($page->url)) {
                $page->url = '/' . str_slug($page->nav_title);
            }
        });

        Page::updating(function($page) {
            if (!$page->external && $page->url[0] !== '/') {
                $page->url = "/{$page->url}";
            }
        });

        Page::deleting(function($page) {
            if ($page->content) $page->content->delete();
        });
    }

    /**
     * Association with PageContent
     */
    public function content()
    {
        return $this->hasOne('App\Models\PageContent');
    }

}
