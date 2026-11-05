<?php

namespace App\Imports\AttendanceImport;

use App\Imports\AbstractMultiSheetImport;
use App\Imports\AttendanceImport\AttendanceRegistersImport;
use App\Models\AttendanceRegister;

class AttendanceRegistersMultiSheetImport extends AbstractMultiSheetImport
{
    protected array $expectedSheetNames = [
        'Attendance Register',
    ];

    protected function getTableName(): string
    {
        return 'attendance_registers';
    }

    protected function getFormName(): string
    {
        return 'Attendance Register Form';
    }

    protected function getModelClass(): string
    {
        return AttendanceRegister::class;
    }

    public function sheets(): array
    {
        return [
            'Attendance Register' => new AttendanceRegistersImport(
                $this->submissionDetails,
                $this->cacheKey,
                $this->totalRows
            ),
        ];
    }
}
