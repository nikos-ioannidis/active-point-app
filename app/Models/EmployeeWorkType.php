<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeWorkType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'work_category_id',
        'work_type_id',
    ];

    /**
     * Get the employee that owns the employee work type.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the work category that owns the employee work type.
     */
    public function workCategory(): BelongsTo
    {
        return $this->belongsTo(WorkCategory::class);
    }

    /**
     * Get the work type that owns the employee work type.
     */
    public function workType(): BelongsTo
    {
        return $this->belongsTo(WorkType::class);
    }
}
