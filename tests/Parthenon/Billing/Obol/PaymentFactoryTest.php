<?php

namespace Parthenon\Billing\Obol;

use Brick\Money\Money;
use Obol\Model\PaymentDetails;
use Obol\Model\SubscriptionCreationResponse;
use Parthenon\Billing\Entity\CustomerInterface;
use PHPUnit\Framework\TestCase;

class PaymentFactoryTest extends TestCase
{
    public function testFromSubscriptionConfirm()
    {
        $customer = $this->createMock(CustomerInterface::class);

        $amount = Money::of(1000, 'USD');

        $paymentDetails = new PaymentDetails();
        $paymentDetails->setAmount($amount);
        $paymentDetails->setPaymentReference('payment-reference');
        $paymentDetails->setStoredPaymentReference('stored-payment-reference');
        $paymentDetails->setCustomerReference('customer-reference');

        $subscriptionCreation = new SubscriptionCreationResponse();
        $subscriptionCreation->setSubscriptionId('subscription-id');
        $subscriptionCreation->setPaymentDetails($paymentDetails);

        $subject = new PaymentFactory();

        $actual =  $subject->fromSubscriptionConfirm($customer, $subscriptionCreation);
        $this->assertTrue($amount->isEqualTo($actual->getMoneyAmount()));
        $this->assertEquals('payment-reference', $actual->getPaymentReference());
        $this->assertSame($customer, $actual->getCustomer());
        $this->assertTrue($actual->isCompleted());
        $this->assertFalse($actual->isRefunded());
        $this->assertFalse($actual->isChargedBack());
    }
}