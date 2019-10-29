<?php

namespace App\Controller\Api;

use App\Service\Email\ContactFormEmail;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

class ContactController extends AbstractFOSRestController
{
    /**
     * @var ContactFormEmail
     */
    private $contactFormEmail;

    public function __construct(ContactFormEmail $contactFormEmail)
    {
        $this->contactFormEmail = $contactFormEmail;
    }

    /**
     * @Rest\Post("/contact/send")
     *
     * @Rest\RequestParam(name="name")
     * @Rest\RequestParam(name="email")
     * @Rest\RequestParam(name="subject")
     * @Rest\RequestParam(name="message")
     *
     * @Rest\View()
     *
     * @param string $name
     * @param string $email
     * @param string $subject
     * @param string $message
     * @return array
     */
    public function send($name, $email, $subject, $message)
    {
        $status = $this->contactFormEmail->send([
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message
        ]);

        return ['status' => $status];
    }
}
