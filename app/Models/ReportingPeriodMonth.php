<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportingPeriodMonth extends Model
{
    use HasFactory;
    protected $table = 'reporting_period_months';
    protected $guarded = [];
    public function reportingPeriod()
    {
        return $this->belongsTo(ReportingPeriod::class, 'period_id');
    }


    // From: Country (Local)
// $this->hasManyThrough(
//     Post::class,  // ➡️ TO: Post (Target/Distant)
//     User::class,  // 🤝 VIA: User (Intermediate)

//     // KEYS ARE DEFINED FROM THE *LOCAL* ENDPOINT MOVING OUTWARDS:
//     'FK_on_users_table',    // The key that connects Country -> User
//     'FK_on_posts_table',    // The key that connects User -> Post
//     'PK_on_countries_table',// (Optional defaults if using 'id')
//     'PK_on_users_table'     // (Optional defaults if using 'id')
// );
    public function submissions()
    {
        // country has post through user

        return $this->hasManyThrough(Submission::class, SubmissionPeriod::class, 'month_range_period_id', 'period_id','id', 'id');
    }

}
