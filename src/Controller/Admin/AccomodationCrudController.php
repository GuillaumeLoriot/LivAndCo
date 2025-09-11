<?php

namespace App\Controller\Admin;

use ApiPlatform\Doctrine\Odm\Filter\BooleanFilter;
use App\Entity\Accomodation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

class AccomodationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Accomodation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),


            ImageField::new('coverPicture', 'Image de couverture')
                ->hideOnForm()
                ->setBasePath('/uploads/images'),

            TextField::new('addressLine1', 'Adresse (ligne 1)'),
            TextField::new('addressLine2', 'Adresse (ligne 2)')->hideOnIndex(),
            TextField::new('zipCode', 'Code postal'),
            TextField::new('city', 'Ville'),
            TextField::new('country', 'Pays'),

            IntegerField::new('surface', 'Surface (m²)'),
            BooleanField::new('mixedGender', 'Mixte'),

            TextField::new('latitude', 'Latitude')->hideOnIndex(),
            TextField::new('longitude', 'Longitude')->hideOnIndex(),

            TextField::new('ownershipDeedPath', 'Acte de propriété')->hideOnIndex(),
            TextField::new('insuranceCertificatePath', 'Attestation d’assurance')->hideOnIndex(),

            TextField::new('coverPicture', 'URL image de couverture')->onlyOnForms(),

            AssociationField::new('images', 'Photos')->onlyOnDetail(),
            AssociationField::new('announcements', 'Annonces')->onlyOnDetail(),

            AssociationField::new('conveniences', 'Équipements')
                ->onlyOnForms()
                ->setFormTypeOptions(['by_reference' => false]),
            AssociationField::new('owner', 'Propriétaire'),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters

            ->add(EntityFilter::new('owner', 'Propriétaire'))
            ->add(NumericFilter::new('surface', 'Surface en m²'))
            ->add(TextFilter::new('city', 'Ville'))
            ->add(TextFilter::new('zipCode', 'Code postal'))
            ->add(
                EntityFilter::new('conveniences', 'Équipements')
                    ->setFormTypeOption('value_type_options', [
                        'choice_label' => 'name',
                        'multiple' => true,
                    ])
            );

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
