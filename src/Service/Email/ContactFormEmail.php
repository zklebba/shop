<?php

namespace App\Service\Email;

class ContactFormEmail extends AbstractSendgridEmail
{
    /**
     * @return array
     */
    public function getDefaultTemplateVars(): array
    {
        return [
            'name' => '',
            'email' => '',
            'subject' => '',
            'message' => '',
        ];
    }
}
