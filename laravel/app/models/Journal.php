<?php

class Journal extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'journals';

    public function user() {
        return $this->belongsTo('User');
    }

    public function scopeOfVolume($query, $volume)
    {
        return $query
                ->where(function($query) use ($volume)
                {
                    $query->whereVolume($volume)
                          ->orWhereRaw("$volume = 0");
                });
    }
}
