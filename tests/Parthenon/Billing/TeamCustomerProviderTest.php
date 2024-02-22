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

namespace Parthenon\Billing;

use Parthenon\Athena\Entity\DeletableInterface;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\EmbeddedSubscription;
use Parthenon\Billing\Exception\NoCustomerException;
use Parthenon\Common\Address;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Entity\Team;
use Parthenon\User\Entity\TeamInterface;
use Parthenon\User\Repository\TeamRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class TeamCustomerProviderTest extends TestCase
{
    public function testNoUser()
    {
        $this->expectException(NoCustomerException::class);

        $security = $this->createMock(Security::class);
        $teamRepository = $this->createMock(TeamRepositoryInterface::class);

        $teamCustomerProvider = new TeamCustomerProvider($security, $teamRepository);
        $teamCustomerProvider->getCurrentCustomer();
    }

    public function testUserIsNotMember()
    {
        $this->expectException(NoCustomerException::class);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn(new class() implements UserInterface {
            public function getRoles(): array
            {
                // TODO: Implement getRoles() method.
            }

            public function eraseCredentials(): void
            {
                // TODO: Implement eraseCredentials() method.
            }

            public function getUserIdentifier(): string
            {
                // TODO: Implement getUserIdentifier() method.
            }
        });
        $teamRepository = $this->createMock(TeamRepositoryInterface::class);

        $teamCustomerProvider = new TeamCustomerProvider($security, $teamRepository);
        $teamCustomerProvider->getCurrentCustomer();
    }

    public function testUserIsMemberButNoTeamFound()
    {
        $member = $this->getMember();
        $this->expectException(NoCustomerException::class);
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($member);

        $teamRepository = $this->createMock(TeamRepositoryInterface::class);
        $teamRepository->method('getByMember')->with($member)->willThrowException(new NoEntityFoundException());

        $teamCustomerProvider = new TeamCustomerProvider($security, $teamRepository);
        $teamCustomerProvider->getCurrentCustomer();
    }

    public function testUserIsMemberButTeamNotCustomer()
    {
        $member = $this->getMember();
        $this->expectException(NoCustomerException::class);
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($member);

        $team = new Team();

        $teamRepository = $this->createMock(TeamRepositoryInterface::class);
        $teamRepository->method('getByMember')->with($member)->willReturn($team);

        $teamCustomerProvider = new TeamCustomerProvider($security, $teamRepository);
        $teamCustomerProvider->getCurrentCustomer();
    }

    public function testUserIsMemberButTeamIsCustomer()
    {
        $member = $this->getMember();
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($member);

        $team = new class() extends Team implements CustomerInterface {
            public function hasSubscription(): bool
            {
                // TODO: Implement hasSubscription() method.
            }

            public function getSubscription(): EmbeddedSubscription
            {
                // TODO: Implement getSubscription() method.
            }

            public function setSubscription(EmbeddedSubscription $subscription)
            {
                // TODO: Implement setSubscription() method.
            }

            public function setBillingAddress(Address $address)
            {
                // TODO: Implement setBillingAddress() method.
            }

            public function getBillingAddress(): Address
            {
                // TODO: Implement getBillingAddress() method.
            }

            public function hasBillingAddress(): bool
            {
                // TODO: Implement hasBillingAddress() method.
            }

            public function hasActiveSubscription(): bool
            {
                // TODO: Implement hasActiveSubscription() method.
            }

            public function setExternalCustomerReference($customerReference)
            {
                // TODO: Implement setExternalCustomerReference() method.
            }

            public function getExternalCustomerReference()
            {
                // TODO: Implement getExternalCustomerReference() method.
            }

            public function getBillingEmail()
            {
                // TODO: Implement getBillingEmail() method.
            }

            public function getDisplayName(): string
            {
                // TODO: Implement getDisplayName() method.
            }

            public function getPaymentProviderDetailsUrl()
            {
                // TODO: Implement getPaymentProviderDetailsUrl() method.
            }

            public function hasExternalCustomerReference(): bool
            {
                return false;
            }

            public function setPaymentProviderDetailsUrl(?string $url)
            {
                // TODO: Implement setPaymentProviderDetailsUrl() method.
            }
        };

        $teamRepository = $this->createMock(TeamRepositoryInterface::class);
        $teamRepository->method('getByMember')->with($member)->willReturn($team);

        $teamCustomerProvider = new TeamCustomerProvider($security, $teamRepository);
        $actual = $teamCustomerProvider->getCurrentCustomer();
        $this->assertEquals($team, $actual);
    }

    protected function getMember(): MemberInterface
    {
        $member = new class() implements UserInterface, MemberInterface {
            public function getRoles(): array
            {
                // TODO: Implement getRoles() method.
            }

            public function eraseCredentials(): void
            {
                // TODO: Implement eraseCredentials() method.
            }

            public function getUserIdentifier(): string
            {
                // TODO: Implement getUserIdentifier() method.
            }

            public function setDeletedAt(\DateTimeInterface $dateTime): DeletableInterface
            {
                // TODO: Implement setDeletedAt() method.
            }

            public function isDeleted(): bool
            {
                // TODO: Implement isDeleted() method.
            }

            public function markAsDeleted(): DeletableInterface
            {
                // TODO: Implement markAsDeleted() method.
            }

            public function unmarkAsDeleted(): DeletableInterface
            {
                // TODO: Implement unmarkAsDeleted() method.
            }

            public function setTeam(TeamInterface $team): MemberInterface
            {
                // TODO: Implement setTeam() method.
            }

            public function getTeam(): TeamInterface
            {
                // TODO: Implement getTeam() method.
            }

            public function setPassword(string $password)
            {
                // TODO: Implement setPassword() method.
            }

            public function getPassword(): ?string
            {
                // TODO: Implement getPassword() method.
            }

            public function setEmail(string $email)
            {
                // TODO: Implement setEmail() method.
            }

            public function getEmail()
            {
                // TODO: Implement getEmail() method.
            }

            public function getName(): ?string
            {
                // TODO: Implement getName() method.
            }

            public function setName(string $name)
            {
                // TODO: Implement setName() method.
            }

            public function setConfirmationCode(string $confirmationCode)
            {
                // TODO: Implement setConfirmationCode() method.
            }

            public function getConfirmationCode()
            {
                // TODO: Implement getConfirmationCode() method.
            }

            public function isConfirmed(): bool
            {
                // TODO: Implement isConfirmed() method.
            }

            public function setIsConfirmed(bool $isConfirmed): void
            {
                // TODO: Implement setIsConfirmed() method.
            }

            public function setCreatedAt(\DateTime $dateTime)
            {
                // TODO: Implement setCreatedAt() method.
            }

            public function setActivatedAt(?\DateTime $dateTime)
            {
                // TODO: Implement setActivatedAt() method.
            }

            public function getId()
            {
                // TODO: Implement getId() method.
            }

            public function setRoles(array $roles)
            {
                // TODO: Implement setRoles() method.
            }
        };

        return $member;
    }
}
