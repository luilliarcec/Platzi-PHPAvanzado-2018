<?php


namespace App\Controllers;


use App\Models\User;
use Exception;
use Respect\Validation\Validator as validation;
use Zend\Diactoros\ServerRequest;

class UsersController extends BaseController
{
    public function getAddUser()
    {
        return $this->renderHTML('admin/addUser.twig');
    }

    public function postSaveUser(ServerRequest $request)
    {
        $errors = null;
        $message = null;
        $postData = $request->getParsedBody();
        $userValidator = validation::key('email', validation::stringType()->notEmpty())
            ->key('email', validation::email())
            ->key('password', validation::stringType()->notEmpty())
            ->key('password', validation::stringType()->length(8, 16));

        try {
            $userValidator->assert($postData); // Valida si está correcto los valores (arroja un exception de no estarlo)

            $user = new User();
            $user->email = $postData['email'];
            $user->password = password_hash($postData['password'], PASSWORD_DEFAULT);
            $user->save();
            $message = 'El registro se ah guardado correctamente';

        } catch (Exception $e) {
            $errors = $e->findMessages([
                'notEmpty' => 'El campo email y password son requerido',
                'email' => 'El {{name}} ingresado no tiene el formato apropiado',
                'length' => 'El {{name}} debe tener mínimo 8 caracteres y máximo 16'
            ]);
        }

        return $this->renderHTML('admin/addUser.twig', [
            'message' => $message,
            'errors' => $errors,
        ]);
    }
}