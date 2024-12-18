<?php

namespace App\Controller;

use App\Entity\Address;
use App\Repository\OrderRepository;
use App\Form\AccountDetailsFormType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function index(AddressRepository $addressRepository, OrderRepository $orderRepo, Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = $this->getUser();
        $userId = $user->getId();
        $addresses = $addressRepository->findByUser($user);
        $orders = $orderRepo->findBy(['userId' => $userId]);
        
        return $this->render('account/index.html.twig', [
            'addresses' => $addresses,
            'orders' => $orders,
        ]);
    }
    #[Route('/account/details', name: 'app_account_details', methods: ['POST'])]
    public function editAccountDetails(EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, Request $request): Response
    {
        
        $user = $this->getUser();
        $data = Request::createFromGlobals()->request;
        $form = $this->createForm(AccountDetailsFormType::class, $user);
        $form->handleRequest($request);
        $userBis = $form->getData();

        $fieldsEmpty = 0;
        $flashes = [
            'succes' => false,
            'message' => ""
        ];
        $userId = $user->getId();

        foreach($data as $field) {
            if (empty(trim($field))) {
                $fieldsEmpty++;
            }
        }

        if (!$fieldsEmpty) {
            if ($userPasswordHasher->isPasswordValid($userBis, $data->get('password'))) {
                if ($data->get('npassword') === $data->get('cpassword')) {
                    if (strlen($data->get('npassword')) >= 6) {
                        $flashes['message'] = 'Updating done!';
                        $flashes['success'] = true;
                        if ($user->getFullName() !== $data->get('name')) {
                            $user->setFullName(strip_tags($data->get('name')));
                        }
                        if ($user->getEmail() !== $data->get('email')) {
                            $user->setEmail(strip_tags($data->get('email')));
                        }
                        $user->setPassword($userPasswordHasher->hashPassword($user, $data->get('npassword')));
                        $entityManager->persist($userBis);
                        $entityManager->flush();
                    } else {
                        $flashes['message'] = 'Your password should be at least 6 characters! ';
                    }
                } else {
                    $flashes['message'] = 'The new passwords are not the same! ';
                }
            } else {
                $flashes['message'] = 'The current password is not valid! ';
            }
        } else {
            $flashes['message'] = 'At least one field is empty!';
        }

        return $this->json($flashes);
    }
}
