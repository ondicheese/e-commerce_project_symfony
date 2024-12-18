<?php

namespace App\Controller\Admin;

use App\Entity\PaymentMethod;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;

class PaymentMethodCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PaymentMethod::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL);
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            TextField::new('description'),
            ImageField::new('imageUrl')
            ->setBasePath('assets/images/payment_methods_logos')
            ->setUploadDir('public/assets/images/payment_methods_logos')
            ->setUploadedFileNamePattern('[randomhash].[extension]'),
            TextEditorField::new('more_description')->hideOnIndex(),
            TextField::new('test_public_api_key')->hideOnIndex(),
            TextField::new('test_private_api_key')->hideOnIndex(),
            TextField::new('prod_public_api_key')->hideOnIndex(),
            TextField::new('prod_private_api_key')->hideOnIndex(),
            TextField::new('testBaseUrl')->hideOnIndex(),
            TextField::new('prodBaseUrl')->hideOnIndex(),
        ];
    }
    
}
