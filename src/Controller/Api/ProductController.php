<?php

namespace App\Controller\Api;

use App\Entity\Product;
use App\Entity\ProductDetails;
use App\Entity\Stock;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

class ProductController extends AbstractFOSRestController
{
    /**
     * @var ProductRepository
     */
    private $productRepo;

    /**
     * @var CategoryRepository
     */
    private $categoryRepo;

    public function __construct(ProductRepository $productRepo, CategoryRepository $categoryRepo)
    {
        $this->productRepo = $productRepo;
        $this->categoryRepo = $categoryRepo;
    }

    /**
     * @Rest\Post("/product")
     *
     * @Rest\RequestParam(name="name", requirements=".{0,30}")
     * @Rest\RequestParam(name="description", requirements=".{0,3000}", default="")
     * @Rest\RequestParam(name="price", requirements="\d+(?:\.\d{1,2})?", default="0")
     * @Rest\RequestParam(name="picture", requirements="(?:http|https):\/\/.*\.(?:jpg|jpeg|png|gif)", default="")
     * @Rest\RequestParam(name="quantity", requirements="\d", default="0")
     * @Rest\RequestParam(name="category", requirements="\d", default="0")
     * @Rest\RequestParam(name="details")

     * @Rest\View()
     *
     * @param string $name
     * @param string $description
     * @param float $price
     * @param string $picture
     * @param integer $quantity
     * @param integer $category
     * @param array $details
     *
     * @throws
     * @return Product
     */
    public function addAction($name, $description = '', $price = 0.0, $picture = '', $quantity = 0, $category = 0, $details = [])
    {
        $product = new Product();

        $product->setName($name);
        $product->setDescription($description);
        $product->setPrice($price);
        $product->setPicture($picture);
        $product->setCategory($category);

        $stock = new Stock();
        $stock->setQuantity($quantity);

        $product->setStock($stock);

        if ($details && is_array($details)) {
            $productDetails = new ProductDetails();

            if (isset($details['title'])) {
                $productDetails->setTitle($details['title']);
            }

            if (isset($details['longDescription'])) {
                $productDetails->setLongDescription($details['longDescription']);
            }

            $product->setDetails($productDetails);
        }

        $this->productRepo->save($product);

        return $product;
    }

    /**
     * @Rest\Post("/products/list")
     *
     * @Rest\RequestParam(map=true, name="products", requirements=@App\Service\Constraint\Product)
     * @Rest\View()
     *
     * @param array $products
     *
     * @throws
     * @return Product[]
     */
    public function addList($products = [])
    {
        $productsEntities = [];

        foreach ($products as $product) {
            $productParams = array_merge([
                'description' => '',
                'price' => 0,
                'picture' => '',
                'quantity' => 1,
                'category' => 0,
                'details' => [],
            ], $product);

            $productEntity = new Product();
            $productEntity->setName($productParams['name']);
            $productEntity->setDescription($productParams['description']);
            $productEntity->setPrice($productParams['price']);
            $productEntity->setPicture($productParams['picture']);

            $category = $this->categoryRepo->getById($productParams['category']);

            $productEntity->setCategory($category);

            $stock = new Stock();
            $stock->setQuantity($productParams['quantity']);

            $productEntity->setStock($stock);

            if (count($productParams['details'])) {
                $details = $productParams['details'];
                $productDetails = new ProductDetails();

                if (isset($details['title'])) {
                    $productDetails->setTitle($details['title']);
                }

                if (isset($details['longDescription'])) {
                    $productDetails->setLongDescription($details['longDescription']);
                }

                $productEntity->setDetails($productDetails);
            }

            $productsEntities[] = $productEntity;
        }

        $this->productRepo->saveList($productsEntities);

        return $productsEntities;
    }

    /**
     * @Rest\Get("/products/list")
     *
     * @Rest\View()
     *
     * @return array()
     */
    public function getListAction()
    {
        return $this->productRepo->getProductsTeasersList();
    }

    /**
     * @Rest\Get("/products/category/{categoryId}")
     *
     * @Rest\View()
     *
     * @param integer $categoryId
     *
     * @return array()
     */
    public function getByCategoryListAction($categoryId)
    {
        return $this->productRepo->getProductsTeasersList($categoryId);
    }

    /**
     * @Rest\Get("/product/{id}")
     *
     * @Rest\View()
     *
     * @throws
     * @param integer $id
     * @return Product
     */
    public function getAction($id)
    {
        return $this->productRepo->getById($id);
    }
}
