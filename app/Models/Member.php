<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    /**
     * The members table uses a custom `create_date` column and has no
     * created_at / updated_at columns, so disable Eloquent timestamps.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'lastname',
        'mail',
        'tckn',
        'lisanceno',
        'status',
    ];
}
