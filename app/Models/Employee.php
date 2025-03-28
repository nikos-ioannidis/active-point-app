<?php

namespace App\Models;

use App\Enums\EmployeeIrataLevelEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    use HasFactory;

    // append the irata level label to the model's attributes
    protected $appends = ['irata_level_label'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'employee_code',
        'employee_name',
        'job_title',
        'phone_number',
        'is_active',
        'owns_equipment',
        'irata_level',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'owns_equipment' => 'boolean',
            'irata_level' => EmployeeIrataLevelEnum::class,
        ];
    }

    /**
     * Get the user that owns the employee.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    // IRATA level label Attribute
    public function getIrataLevelLabelAttribute(): string
    {
        if(empty($this->irata_level)) {
            return '';
        }

        // get enum label from enum class
        return $this->irata_level?->label();
    }
}
