<?php


namespace App\Controllers;


use PhpParser\Node\Expr\Array_;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\ServerRequest;

class ContactController extends BaseController
{
    public function index()
    {
        return $this->renderHTML('contact.twig');
    }

    public function sent(ServerRequest $request)
    {
        // Create the Transport
        $transport = (new Swift_SmtpTransport(getenv('SMTP_HOST'), getenv('SMTP_PORT')))
            ->setEncryption(getenv('SMTP_ENCRYPTION'))
            ->setUsername(getenv('SMTP_USER'))
            ->setPassword(getenv('SMTP_PASS'));

        // Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($transport);

        // Create a message
        $message = (new Swift_Message('Correo de tu sitio web :D'))
            ->setFrom([getenv('SMTP_USER') => 'Website Luis Arce'])
            ->setTo(['luilliarcec@outlook.com'])
            ->setBody($this->formatterMessage($request), 'text/html');

        // Send the message
        $result = $mailer->send($message);
        return new RedirectResponse('/contact');
    }

    private function formatterMessage(ServerRequest $request)
    {
        $requestData = $request->getParsedBody();
        $message = "Mi nombre es: $requestData[name]\nMi correo es: $requestData[email]\n\n$requestData[message]";
        return $message;
    }
}