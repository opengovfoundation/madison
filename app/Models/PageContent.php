<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Page;

class PageContent extends Model
{
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
}
