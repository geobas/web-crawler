<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'url',
    ];

    /**
     * A Site has many Pages.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pages()
    {
    	return $this->hasMany('App\Page');
    }

    /**
     * Retrieve all crawled pages of a site.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $url
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRetrievePages($query, $url)
    {
        return $query->with('pages')
                     ->whereUrl($url)
                     ->first()
                     ->pages
                     ->pluck('body', 'name');
    }
}
