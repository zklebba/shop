<?php

namespace App\Service\Email;

use SendGrid;
use SendGrid\Mail\Mail;

class SendgridEmailConfigurator
{
    private $config;

    /**
     * @var Mail
     */
    private $email;

    /**
     * @var SendGrid
     */
    private $sendgrid;

    public function __construct(SendGrid $sendGrid, Mail $sendGridMail, $config = [])
    {
        $this->sendgrid = $sendGrid;
        $this->email = $sendGridMail;
        $this->config = $config;
    }

    public function configure(AbstractSendgridEmail $email)
    {
        $email->setConfig($this->config);
        $email->setEmail($this->email);
        $email->setSendgrid($this->sendgrid);
    }
}