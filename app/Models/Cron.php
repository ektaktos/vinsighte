<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cron extends Model
{
    use HasFactory;
    protected $primaryKey = 'command';
    protected $fillable = ['command', 'next_run', 'last_run'];
}
