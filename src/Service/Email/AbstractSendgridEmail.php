<?php

namespace App\Service\Email;

use SendGrid;
use SendGrid\Mail\Mail;

abstract class AbstractSendgridEmail
{
    /**
     * @var Mail
     */
    private $email = null;

    /**
     * @var SendGrid
     */
    private $sendgrid = null;

    /**
     * @var string
     */
    private $templateId = '';

    /**
     * @var string
     */
    private $subject = '';

    /**
     * @var array $config
     */
    private $config = [];

    /**
     * @var array
     */
    private $to = [];

    /**
     * Returns default email template variables
     *
     * @return array
     */
    abstract function getDefaultTemplateVars(): array;

    /**
     * Send email
     *
     * @param array $templateVars
     * @return bool
     * @throws SendGrid\Mail\TypeException
     */
    public function send($templateVars = [])
    {
        if ($this->config['from_email']) {
            $this->email->setFrom($this->config['from_email'], $this->config['from_name'] ?: '');
        }

        if ($to = $this->getTo()) {
            $this->email->addTo($to['email'], $to['name']);
        }

        if ($templateId = $this->getTemplateId()) {
            $this->email->setTemplateId($templateId);
        }

        if ($subject = $this->getSubject()) {
            $templateVars = array_merge([
                'message_title' => $subject,
                'email_subject' => $subject,
            ], $templateVars);
        }

        $this->email->addDynamicTemplateDatas(array_merge($this->getDefaultTemplateVars(), $templateVars));

        try {
            $this->sendgrid->send($this->email);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return Mail
     */
    public function getEmail(): Mail
    {
        return $this->email;
    }

    /**
     * @param Mail $email
     */
    public function setEmail(Mail $email): void
    {
        $this->email = $email;
    }

    /**
     * @return SendGrid
     */
    public function getSendgrid(): SendGrid
    {
        return $this->sendgrid;
    }

    /**
     * @param SendGrid $sendgrid
     */
    public function setSendgrid(SendGrid $sendgrid): void
    {
        $this->sendgrid = $sendgrid;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getTemplateId(): string
    {
        return $this->templateId;
    }

    /**
     * @param string $templateId
     */
    public function setTemplateId(string $templateId): void
    {
        $this->templateId = $templateId;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return array
     */
    public function getTo(): array
    {
        return $this->to;
    }

    /**
     * @param $email
     * @param string $name
     */
    public function setTo($email, $name = ''): void
    {
        $this->to = [
            'email' => $email,
            'name' => $name,
        ];
    }
}
