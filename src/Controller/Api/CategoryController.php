<?php

namespace App\Controller\Api;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

class CategoryController extends AbstractFOSRestController
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepo;

    public function __construct(CategoryRepository $repository)
    {
        $this->categoryRepo = $repository;
    }

    /**
     * @Rest\Post("/category")
     *
     * @Rest\RequestParam(name="name", requirements=".{0,30}")

     * @Rest\View()
     *
     * @param string $name
     *
     * @throws
     * @return Category
     */
    public function addAction($name)
    {
        $category = new Category();

        $category->setName($name);

        $this->categoryRepo->save($category);

        return $category;
    }

    /**
     * @Rest\Post("/category/list")
     *
     * @Rest\RequestParam(map=true, name="categories")
     * @Rest\View()
     *
     * @param array $categories
     *
     * @throws
     * @return Category[]
     */
    public function addList($categories = [])
    {
        $categoryEntities = [];

        foreach ($categories as $category) {
            $categoryParams = array_merge([
                'name' => '',
            ], $category);

            $category = new Category();
            $category->setName($categoryParams['name']);

            $categoryEntities[] = $category;
        }

        $this->categoryRepo->saveList($categoryEntities);

        return $categoryEntities;
    }

    /**
     * @Rest\Get("/category")
     *
     * @Rest\View()
     *
     * @return Category[]
     */
    public function getListAction()
    {
        return $this->categoryRepo->getList();
    }

    /**
     * @Rest\Get("/category/{id}")
     *
     * @Rest\View()
     *
     * @throws
     * @param integer $id
     * @return Category
     */
    public function getAction($id)
    {
        return $this->categoryRepo->getById($id);
    }
}
