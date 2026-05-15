<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Problem extends Model
{
    protected $fillable = ['question_text', 'countdown_seconds'];

    public function slots(): HasMany
    {
        return $this->hasMany(ProblemSlot::class)->orderBy('slot_number');
    }
}
