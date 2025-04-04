<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyReport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'report_date',
        'work_job_id',
        'vehicle_id',
        'notes',
        'total_minutes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'report_date' => 'date',
        'total_minutes' => 'integer',
    ];

    /**
     * Get the employee that owns the daily report.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the vehicle that is used in the daily report.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the work entries for the daily report.
     */
    public function workEntries(): HasMany
    {
        return $this->hasMany(DailyReportWorkEntry::class);
    }

    /**
     * Get the work job associated with the daily report.
     */
    public function workJob(): BelongsTo
    {
        return $this->belongsTo(WorkJob::class);
    }
}
