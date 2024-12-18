<?php
namespace App\Services;

use App\Entity\PaymentMethod;
use App\Repository\PaymentMethodRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class PaypalService
{
    private $session;

    public function __construct(private RequestStack $requestStack, private PaymentMethodRepository $paymentMethodRepo)
    {
        $this->session = $requestStack->getSession();
    }
    
    public function getPublicKey() 
    {
        $config = $this->paymentMethodRepo->findOneByName('Paypal');
        
        if ($_ENV['APP_ENV'] === 'dev') {
            return $config->getTestPublicApiKey();
        } else {
            return $config->getProdPublicApiKey();
        }
    }

    public function getPrivateKey(): string
    {
        $config = $this->paymentMethodRepo->findOneByName('Paypal');
        if ($_ENV['APP_ENV'] === 'dev') {
            return $config->getTestPrivateApiKey();
        } else {
            return $config->getProdPrivateApiKey();
        }
    }

    public function getBaseUrl(): string
    {
        $config = $this->paymentMethodRepo->findOneByName('Paypal');

        if ($_ENV['APP_ENV'] === 'dev') {
            return $config->getTestBaseUrl();
        } else {
            return $config->getProdBaseUrl();
        }
    }
}