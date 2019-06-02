<?php


namespace App\Services;


use App\Models\Job;

class JobService
{
    public function deleteJob(int $id)
    {
        $job = Job::findOrFail($id);
        $job->delete();
    }
}