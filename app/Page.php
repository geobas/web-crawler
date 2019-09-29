<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'site_id',
        'name',
        'body',
    ];

    /**
     * A Page belongs to a Site.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function site()
    {
    	return $this->belongsTo('App\Site');
    }
}
