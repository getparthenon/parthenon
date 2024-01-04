<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2024
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Common;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Embeddable]
class Address
{
    #[SerializedName('company_name')]
    #[ORM\Column(nullable: true)]
    private ?string $companyName = '';

    #[SerializedName('street_line_one')]
    #[ORM\Column(nullable: true)]
    private ?string $streetLineOne = '';

    #[SerializedName('street_line_two')]
    #[ORM\Column(nullable: true)]
    private ?string $streetLineTwo = '';

    #[SerializedName('city')]
    #[ORM\Column(nullable: true)]
    private ?string $city = '';

    #[SerializedName('region')]
    #[ORM\Column(nullable: true)]
    private ?string $region = '';

    #[SerializedName('country')]
    #[ORM\Column(nullable: true)]
    private ?string $country = '';

    #[SerializedName('post_code')]
    #[ORM\Column(nullable: true)]
    private ?string $postcode = '';

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): void
    {
        $this->companyName = $companyName;
    }

    public function hasCompanyName(): bool
    {
        return isset($this->companyName) && !empty($this->companyName);
    }

    public function getStreetLineOne(): ?string
    {
        return $this->streetLineOne;
    }

    public function setStreetLineOne(?string $streetLineOne): void
    {
        $this->streetLineOne = $streetLineOne;
    }

    public function hasStreetLineOne(): bool
    {
        return isset($this->streetLineOne) && !empty($this->streetLineOne);
    }

    public function getStreetLineTwo(): ?string
    {
        return $this->streetLineTwo;
    }

    public function setStreetLineTwo(?string $streetLineTwo): void
    {
        $this->streetLineTwo = $streetLineTwo;
    }

    public function hasStreetLineTwo(): bool
    {
        return isset($this->streetLineTwo) && !empty($this->streetLineTwo);
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function hasCity(): bool
    {
        return isset($this->city) && !empty($this->city);
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): void
    {
        $this->region = $region;
    }

    public function hasRegion(): bool
    {
        return isset($this->region) && !empty($this->region);
    }

    public function getCountry(): ?string
    {
        if (!isset($this->country)) {
            return '';
        }

        return $this->country;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    public function hasCountry(): bool
    {
        return isset($this->country) && !empty($this->country);
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(?string $postcode): void
    {
        $this->postcode = $postcode;
    }

    public function hasPostcode(): bool
    {
        return isset($this->postcode) && !empty($this->postcode);
    }
}
