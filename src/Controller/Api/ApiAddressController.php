<?php

namespace App\Controller\Api;

use App\Entity\Address;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
class ApiAddressController extends AbstractController
{
    #[Route('/address', name: 'app_post_address', methods: ['POST'])]
    public function index(EntityManagerInterface $manager, AddressRepository $addressRepository): Response
    {
        $request = Request::createFromGlobals()->request;
        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                'isSuccess' => false,
                'message' => 'No authorization !',
                'data' => []
            ]);
        }
        
        $address = new Address();
        
        $address->setName($request->get('name'))
            ->setClientName($request->get('client_name'))
            ->setStreet($request->get('street'))
            ->setAddressType($request->get('address_type'))
            ->setPostcode($request->get('postcode'))
            ->setCity($request->get('city'))
            ->setState($request->get('state'))
            ->setUser($user);

            $manager->persist($address);
            $manager->flush();

            $addresses = $addressRepository->findByUser($user);

            foreach ($addresses as $key => $address) {
                $address->setUser(null);
                $addresses[$key] = $address;
            }

            return $this->json([
                "data" => $addresses,
                "isSuccess" => true
            ]);
    }

    #[Route('/address/{addressId}', name: 'app_post_delete_address', methods: ['DELETE'])]
    public function deleteAddress(string $addressId, EntityManagerInterface $manager, AddressRepository $addressRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                'isSuccess' => false,
                'message' => 'No authorization !',
                'data' => []
            ]);
        }

        $address = $addressRepository->find($addressId);

        if (!$address) {
            return $this->json([
                'isSuccess' => false,
                'message' => 'Address not found !',
                'data' => []
            ]);
        }
        
        if ($user !== $address->getUser()) {
            return $this->json([
                'isSuccess' => false,
                'message' => 'No authorization !',
                'data' => []
            ]);
        }

        $manager->remove($address);
        $manager->flush();

        $addresses = $addressRepository->findByUser($user);
        
        foreach ($addresses as $key => $address) {
            $address->setUser(null);
            $addresses[$key] = $address;
        }

        return $this->json([
            "data" => $addresses,
            "isSuccess" => true
        ]);
    }
    #[Route('/address/{addressId}', name: 'app_post_put_address', methods: ['POST'])]
    public function updateAddress(string $addressId, EntityManagerInterface $manager, AddressRepository $addressRepository): Response
    {
        $user = $this->getUser();
        $request = Request::createFromGlobals();
        
        if (!$user) {
            return $this->json([
                'isSuccess' => false,
                'message' => 'No authorization !',
                'data' => []
            ]);
        }

        $address = $addressRepository->find($addressId);

        if (!$address) {
            return $this->json([
                'isSuccess' => false,
                'message' => 'Address not found !',
                'data' => []
            ]);
        }
        
        if ($user !== $address->getUser()) {
            return $this->json([
                'isSuccess' => false,
                'message' => 'No authorization !',
                'data' => []
            ]);
        }

        $address->setName($request->get('name'))
            ->setClientName($request->get('client_name'))
            ->setStreet($request->get('street'))
            ->setAddressType($request->get('address_type'))
            ->setPostcode($request->get('postcode'))
            ->setCity($request->get('city'))
            ->setState($request->get('state'));

        $manager->persist($address);
        $manager->flush();

        $addresses = $addressRepository->findByUser($user);
        
        foreach ($addresses as $key => $address) {
            $address->setUser(null);
            $addresses[$key] = $address;
        }

        return $this->json([
            "data" => $addresses,
            "isSuccess" => true
        ]);
        
    }
}
