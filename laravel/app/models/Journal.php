<?php

class Journal extends Enloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'journals';

    public function user() {
        return $this->belongsTo('User');
    }

}
