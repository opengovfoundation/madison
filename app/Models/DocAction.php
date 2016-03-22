<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocAction extends Model
{

	public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function doc()
    {
        return $this->belongsTo('App\Models\Doc', 'doc_id');
    }
}
