<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function author() {
        return $this->belongsTo(User::class);
    }

    public function versions() {
        return $this->hasMany(GameVersion::class, 'version');
    }

    public function scores() {
        return $this->hasManyThrough(Score::class, GameVersion::class);
    }
}
