<?php


namespace App\Controllers;


use App\Models\User;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\ServerRequest;

class AuthController extends BaseController
{
    public function getLogin()
    {
        return $this->renderHTML('login.twig');
    }

    public function postLogin(ServerRequest $request)
    {
        $postData = $request->getParsedBody();

        $responseMessage = null;
        $user = User::where('email', $postData['email'])->first();

        if ($user) { //Verdadero si es diferente de null o vacio
            if (password_verify($postData['password'], $user->password)) {
                $_SESSION['userId'] = $user->id;
                return new RedirectResponse('admin');
            } else {
                $responseMessage = 'Credenciales incorrectas';
            }
        } else {
            $responseMessage = 'Credenciales incorrectas';
        }

        return $this->renderHTML('login.twig', [
            'responseMessage' => $responseMessage
        ]);
    }

    public function getLogout()
    {
        session_destroy();
        return new RedirectResponse(BASE_URL.'login');
    }

    public function getAdmin(){
        $session = $_SESSION['userId'] ?? null;
        if($session) {
            return new RedirectResponse('admin');
        }
    }
}