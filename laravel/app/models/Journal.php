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
        return $query->whereVolume($volume);
    }
}
