<?php

namespace App\Service\Email;

use App\Service\OrdersAccessKeyService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class OrdersAccessEmail extends AbstractSendgridEmail
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var string
     */
    private $routeName;

    /**
     * @var OrdersAccessKeyService
     */
    private $accessKeyGenerator;

    public function __construct(
        OrdersAccessKeyService $accessKey,
        RouterInterface $router,
        string $routeName
    ) {
        $this->router = $router;
        $this->accessKeyGenerator = $accessKey;
        $this->routeName = $routeName;
    }

    /**
     * @return array
     */
    public function getDefaultTemplateVars(): array
    {
        return [
            'link' => '',
        ];
    }

    public function send($email = '')
    {
        $this->setTo($email);

        $code = $this->accessKeyGenerator->generateAccessKey($email);

        $link = $this->router->generate($this->routeName, [
            'accessCode' => $code,
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        return parent::send([
            'link' => $link,
        ]);
    }
}
