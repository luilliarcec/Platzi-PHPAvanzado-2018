<?php


namespace App\Controllers;


use App\Models\Project;
use Exception;
use Respect\Validation\Validator as validation;
use Zend\Diactoros\ServerRequest;

class ProjectsController extends BaseController
{
    public function getAddProject()
    {
        return $this->renderHTML('admin/projects/addProject.twig');
    }

    public function postSaveProject(ServerRequest $request)
    {
        $postData = $request->getParsedBody();

        $projectValidator = validation::key('title', validation::stringType()->notEmpty())
            ->key('description', validation::stringType()->notEmpty());
//            ->key('months', validation::numeric()->positive())
//            ->key('months', validation::intVal()->min(0, true));

        try {
            $projectValidator->assert($postData); // true

            $files = $request->getUploadedFiles();
            $image = $files['image'];
            $rutaImg = null;
            if ($image->getError() == UPLOAD_ERR_OK) {
                $fileName = $image->getClientFilename();
                $rutaImg = "uploads/$fileName";
                $image->moveTo($rutaImg);
            }

            $project = new Project();
            $project->title = $postData['title'];
            $project->description = $postData['description'];
            $project->imageUrl = $rutaImg;
            $project->months = $postData['tiempo'];
            $project->save();

            $responseMessage = 'Guardado';
        } catch (Exception $e) {
            $responseMessage = $e->getMessage();
        }

        return $this->renderHTML('admin/projects/addProject.twig', [
            'responseMessage' => $responseMessage
        ]);
    }
}