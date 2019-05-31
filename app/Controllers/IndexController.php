<?php

namespace App\Controllers;

use App\Models\{Job, Project};

class IndexController extends BaseController
{
    public function IndexAction()
    {
        
        $jobs = Job::all();
        $projects = Project::all();

        $lastname = 'Arce';
        $name = "Luis $lastname";
        $limitMonths = 2000;

        return $this->renderHTML('index.twig', [
            'name' => $name,
            'jobs' => $jobs,
            'projects' => $projects,
        ]);
    }
}
