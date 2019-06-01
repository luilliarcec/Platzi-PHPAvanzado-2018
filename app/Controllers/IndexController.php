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

        // Closure dentro de una funcion
//        $jobs = array_filter($jobs->toArray(), function ($job) use ($limitMonths) {
//            return $job['months'] > $limitMonths;
//        });

        // No permite usar el trait
//        $jobs = array_filter($jobs->toArray(), $filterFunc);

        // Permite usar trait de manera correcta y elimina todos los que cumplan la condicion en nuestra lista de jobs
        $jobs = $jobs->reject($filterFunc);

        $lastname = 'Arce';
        $name = "Luis $lastname";

        return $this->renderHTML('index.twig', [
            'name' => $name,
            'jobs' => $jobs,
            'projects' => $projects,
        ]);
    }
}
