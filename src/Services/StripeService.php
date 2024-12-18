<?php
namespace App\Services;

use App\Entity\PaymentMethod;
use App\Repository\PaymentMethodRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class StripeService
{
    private $session;

    public function __construct(private RequestStack $requestStack, private PaymentMethodRepository $paymentMethodRepo)
    {
        $this->session = $requestStack->getSession();
    }
    
    public function getPublicKey() 
    {
        $config = $this->paymentMethodRepo->findOneByName('Stripe');
        
        if ($_ENV['APP_ENV'] === 'dev') {
            return $config->getTestPublicApiKey();
        } else {
            return $config->getProdPublicApiKey();
        }
    }

    public function getPrivateKey() 
    {
        $config = $this->paymentMethodRepo->findOneByName('Stripe');
        if ($_ENV['APP_ENV'] === 'dev') {
            return $config->getTestPrivateApiKey();
        } else {
            return $config->getProdPrivateApiKey();
        }
    }
}