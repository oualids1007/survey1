<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProtectedUrl extends Model
{
    protected $table = 'protected_urls';

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }
}
