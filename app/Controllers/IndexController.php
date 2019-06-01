<?php

namespace App\Controllers;

use App\Models\{Job, Project};

class IndexController extends BaseController
{
    public function IndexAction()
    {

        $jobs = Job::all();
        $projects = Project::all();

        $limitMonths = 8;

        // Closure o funciones anonimas dentro de una variable
        $filterFunc = function ($job) use ($limitMonths) {
            return $job['months'] > $limitMonths;
        };

        // Closure dentro de una funcion
//        $jobs = array_filter($jobs->toArray(), function ($job) use ($limitMonths) {
//            return $job['months'] > $limitMonths;
//        });

        $jobs = array_filter($jobs->toArray(), $filterFunc);

        $lastname = 'Arce';
        $name = "Luis $lastname";

        return $this->renderHTML('index.twig', [
            'name' => $name,
            'jobs' => $jobs,
            'projects' => $projects,
        ]);
    }
}
