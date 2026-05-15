<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProblemSlot extends Model
{
    protected $fillable = ['problem_id', 'slot_number', 'image_path', 'answer_text', 'is_correct'];

    protected $casts = ['is_correct' => 'boolean'];

    public function problem(): BelongsTo
    {
        return $this->belongsTo(Problem::class);
    }

    public function imageUrl(): ?string
    {
        if (!$this->image_path) return null;
        return asset('storage/' . $this->image_path);
    }
}
