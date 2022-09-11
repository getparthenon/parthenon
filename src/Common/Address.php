<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Address
{
    /**
     * @ORM\Column(nullable=true)
     */
    private string $companyName = '';

    /**
     * @ORM\Column(nullable=true)
     */
    private string $streetLineOne = '';

    /**
     * @ORM\Column(nullable=true)
     */
    private string $streetLineTwo = '';

    /**
     * @ORM\Column(nullable=true)
     */
    private string $city = '';

    /**
     * @ORM\Column(nullable=true)
     */
    private string $region = '';

    /**
     * @ORM\Column(nullable=true)
     */
    private string $country = '';

    /**
     * @ORM\Column(nullable=true)
     */
    private string $postcode = '';

    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): void
    {
        $this->companyName = $companyName;
    }

    public function getStreetLineOne(): string
    {
        return $this->streetLineOne;
    }

    public function setStreetLineOne(string $streetLineOne): void
    {
        $this->streetLineOne = $streetLineOne;
    }

    public function getStreetLineTwo(): string
    {
        return $this->streetLineTwo;
    }

    public function setStreetLineTwo(string $streetLineTwo): void
    {
        $this->streetLineTwo = $streetLineTwo;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    public function getCountry(): string
    {
        if (!isset($this->country)) {
            return '';
        }

        return $this->country;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    public function getPostcode(): string
    {
        return $this->postcode;
    }

    public function setPostcode(string $postcode): void
    {
        $this->postcode = $postcode;
    }
}
