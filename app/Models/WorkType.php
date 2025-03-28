<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'work_category_id',
        'name',
        'price_standard',
        'price_gamesa',
        'price_gamesa_abroad',
        'max_hours',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price_standard' => 'decimal:2',
            'price_gamesa' => 'decimal:2',
            'price_gamesa_abroad' => 'decimal:2',
            'max_hours' => 'integer',
        ];
    }

    /**
     * Get the work category that owns the work type.
     */
    public function workCategory(): BelongsTo
    {
        return $this->belongsTo(WorkCategory::class);
    }
}
