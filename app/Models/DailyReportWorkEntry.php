<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyReportWorkEntry extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'daily_report_id',
        'work_type_id',
        'start_time',
        'end_time',
        'description',
    ];

    /**
     * Get the daily report that owns the work entry.
     */
    public function dailyReport(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class);
    }

    /**
     * Get the work type that is used in the work entry.
     */
    public function workType(): BelongsTo
    {
        return $this->belongsTo(WorkType::class);
    }
}
