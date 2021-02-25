<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    // attributes that must be available in data  .
    protected $fillable = [
        'name',
        'status',
        'date_task',
        'user_id'
    ];


    // Task belongs  to a User.
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
