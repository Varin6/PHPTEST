<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    public $nextCard;
    public $health;
    public $score;

    use HasFactory;
}
