<?php

namespace App\Traits;

use App\Models\User;
use App\Models\JobProgress;
use Illuminate\Support\Facades\Cache;

trait CheckProgressTrait
{
    //

    public function checkProgress()
    {
        $jobProgress = JobProgress::where('cache_key', $this->importId)->first();

        $this->progress = $jobProgress ? $jobProgress->progress : 0;
        $this->importing = true;
        $this->importingFinished = false;

        if ($jobProgress && $jobProgress->status == 'failed') {
            Cache::forget($this->importId);
            session()->flash('error', 'An error occurred during the import! --- ' . $jobProgress->error);

            $this->redirect(url()->previous());
        } else if ($jobProgress && $jobProgress->status == 'completed') {


            $user = User::find(auth()->user()->id);
            Cache::forget($this->importId);

            if ($user->hasAnyRole('external')) {
                session()->flash('success', 'Successfully submitted!');
                $this->redirect(route('external-submissions') . '#batch-submission');
            } else if ($user->hasAnyRole('staff')) {
                session()->flash('success', 'Successfully submitted!');
                $this->redirect(route('cip-staff-submissions') . '#batch-submission');
            } else if ($user->hasAnyRole('admin')) {
                session()->flash('success', 'Successfully submitted!');
                $this->redirect(route('admin-submissions') . '#batch-submission');
            } else {
                session()->flash('success', 'Successfully submitted!');
                $this->redirect(route('cip-submissions') . '#batch-submission');
            }
        }
    }
}
