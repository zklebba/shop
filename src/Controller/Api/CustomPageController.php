<?php

namespace App\Controller\Api;

use App\Entity\CustomPage;
use App\Repository\CustomPageRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

class CustomPageController extends AbstractFOSRestController
{
    /**
     * @var CustomPageRepository
     */
    private $customPageRepo;

    public function __construct(CustomPageRepository $customPageRepo)
    {
        $this->customPageRepo = $customPageRepo;
    }

    /**
     * @Rest\Post("/page")
     *
     * @Rest\RequestParam(name="name")
     * @Rest\RequestParam(name="content")
     *
     * @Rest\View()
     *
     * @throws
     * @param string $name
     * @param string $content
     * @return CustomPage
     */
    public function create($name, $content)
    {
        $page = new CustomPage();
        $page->setName($name);
        $page->setContent($content);

        $this->customPageRepo->save($page);

        return $page;
    }

    /**
     * @Rest\Get("/page/list")
     *
     * @Rest\View()
     * @return array
     */
    public function getPagesList()
    {
        return $this->customPageRepo->getPagesList();
    }

    /**
     * @Rest\Get("/page/{pageId}")
     *
     * @param int $pageId
     * @Rest\View()
     * @return CustomPage
     */
    public function getPage($pageId)
    {
        return $this->customPageRepo->find($pageId);
    }
}
