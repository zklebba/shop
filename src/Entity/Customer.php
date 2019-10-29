<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CustomerRepository")
 */
class Customer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $billingAddress = [];

    /**
     * @ORM\Column(type="json")
     */
    private $shippingAddress = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getBillingAddress(): ?array
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(array $billingAddress): self
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    public function getShippingAddress(): ?array
    {
        return $this->shippingAddress;
    }

    public function setShippingAddress(array $shippingAddress): self
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }
}
