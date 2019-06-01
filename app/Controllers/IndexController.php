<?php

namespace App\Controllers;

use App\Models\{Job, Project};

class IndexController extends BaseController
{
    public function IndexAction()
    {
        $jobs = Job::all();
        $projects = Project::all();

        $limitMonths = 0;

        // Closure o funciones anonimas dentro de una variable
        $filterFunc = function ($job) use ($limitMonths) {
            return $job['months'] < $limitMonths;
        };

        $jobs = $jobs->reject($filterFunc);

        $lastname = 'Arce';
        $name = "Luis $lastname";

        return $this->renderHTML('index.twig', [
            'name' => $name,
            'jobs' => $jobs,
            'projects' => $projects,
        ]);
    }

    public function get404()
    {
        return $this->renderHTML('404.twig'); // No implementado
    }
}
