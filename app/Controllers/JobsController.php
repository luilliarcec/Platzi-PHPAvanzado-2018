<?php

namespace App\Controllers;

use App\Models\Job;
use Exception;
use Respect\Validation\Validator as validation;
use Zend\Diactoros\ServerRequest;

class JobsController extends BaseController
{
    public function getIndex()
    {
        $jobs = Job::all();
        return $this->renderHTML('admin/jobs/index.twig', compact('jobs'));
    }

    public function getAddJobAction(ServerRequest $request)
    {
        $responseMessage = null;
        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();

            $jobValidator = validation::key('title', validation::stringType()->notEmpty())
                ->key('description', validation::stringType()->notEmpty());

            try {
                $jobValidator->assert($postData); // true

                $files = $request->getUploadedFiles();
                $image = $files['image'];
                $rutaImg = null;
                if ($image->getError() == UPLOAD_ERR_OK) {
                    $fileName = $image->getClientFilename();
                    $rutaImg = "uploads/$fileName";
                    $image->moveTo($rutaImg);
                }

                $job = new Job();
                $job->title = $postData['title'];
                $job->description = $postData['description'];
                $job->imageUrl = $rutaImg;
                $job->visible = isset($postData['visible']) ? true : false;
                $job->months = $postData['tiempo'];
                $job->save();

                $responseMessage = 'Guardado';
            } catch (Exception $e) {
                $responseMessage = $e->getMessage();
            }
        }

        return $this->renderHTML('admin/jobs/addJob.twig', [
            'responseMessage' => $responseMessage
        ]);
    }
}