<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),

            ImageField::new('profilePicture', 'Photo de profil')
                ->setBasePath('/uploads/profile-pictures'),

            EmailField::new('email', 'E-mail'),

            TextField::new('firstName', 'Prénom utilisateur'),
            TextField::new('lastName', 'Nom utilisateur'),

            ArrayField::new('roles', 'Rôles')
                ->setHelp('ROLE_ADMIN , ROLE_OWNER, ROLE_USER'),

            DateTimeField::new('createdAt', 'Créé le')
                ->hideOnForm(),

            BooleanField::new('isVerified', 'Vérifié'),

            ArrayField::new('accomodations', 'Logements')
                ->onlyOnDetail(),

            AssociationField::new('sentMessages', 'Messages envoyés')
                ->onlyOnDetail(),
            AssociationField::new('receivedMessages', 'Messages reçus')
                ->onlyOnDetail(),
            AssociationField::new('reservations', 'Réservations')
                ->onlyOnDetail(),
            AssociationField::new('reviews', 'Avis')
                ->onlyOnDetail(),
        ];
    }

    
    public function configureActions(Actions $actions): Actions
    {

        $detail = Action::new(Action::DETAIL, 'Voir', 'fas fa-eye')
            ->linkToCrudAction(Action::DETAIL);

        return $actions
            ->add(Crud::PAGE_INDEX, $detail)
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action
                    ->setIcon('fas fa-pen')
                    ->setLabel('Edit');
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action
                    ->setIcon('fas fa-trash')
                    ->setLabel('Supprimer');
            });

    }

}
