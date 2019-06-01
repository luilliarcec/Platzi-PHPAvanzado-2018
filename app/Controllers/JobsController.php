<?php

namespace App\Controllers;

use App\Models\Job;
use Exception;
use Respect\Validation\Validator as validation;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\ServerRequest;

class JobsController extends BaseController
{
    public function getIndex()
    {
        $jobs = Job::withTrashed()->get();
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

    public function deleteAction(ServerRequest $request)
    {
        $params = $request->getQueryParams();
        $job = Job::find($params['id']);
        if ($job) {
            $job->delete();
        }
        return new RedirectResponse('/jobs');
    }

    public function hardDeleteAction(ServerRequest $request)
    {
        $params = $request->getQueryParams();
        $job = Job::withTrashed()->where('id', $params['id']);
        if ($job) {
            $job->forceDelete();
        }
        return new RedirectResponse('/jobs');
    }

    public function restoreAction(ServerRequest $request)
    {
        $params = $request->getQueryParams();
        $job = Job::onlyTrashed()->where('id', $params['id']);
        if ($job) {
            $job->restore();
        }
        return new RedirectResponse('/jobs');
    }
}