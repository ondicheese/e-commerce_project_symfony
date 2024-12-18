<?php

namespace App\Controller\Admin;

use App\Entity\Collection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class CollectionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Collection::class;
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
            TextField::new('title'),
            TextField::new('description'),
            TextField::new('button_text'),
            TextField::new('button_link'),
            ImageField::new('imageUrl')
                ->setBasePath('assets/images/collections')
                ->setUploadDir('public/assets/images/collections')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired($pageName === Crud::PAGE_NEW),
            BooleanField::new('isMega')
        ];
    }
}
