<?php

namespace App\Controller\Admin;

use App\Entity\Announcement;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;

class AnnouncementCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Announcement::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),

            ImageField::new('coverPicture', 'Image de couverture')
                ->hideOnForm()
                ->setBasePath('/uploads/images'),

            TextField::new('title', 'Titre'),
            TextEditorField::new('description', 'Description')->hideOnIndex(),

            IntegerField::new('dailyPrice', 'Prix/jour (€)'),
            IntegerField::new('nbPlace', 'Places'),

            AssociationField::new('accomodation', 'Logement')
                ->autocomplete()
                ->hideOnForm(),

            AssociationField::new('conveniences', 'Équipements')
                ->onlyOnForms()
                ->setFormTypeOptions(['by_reference' => false]),
            AssociationField::new('services', 'Services')
                ->onlyOnForms()
                ->setFormTypeOptions(['by_reference' => false]),

            AssociationField::new('reservations', 'Réservations')->onlyOnDetail(),
            AssociationField::new('unavailabilities', 'Indisponibilités')->onlyOnDetail(),
            AssociationField::new('conveniences', 'Équipements')->onlyOnDetail(),
            AssociationField::new('services', 'Services')->onlyOnDetail(),

            TextField::new('coverPicture', 'URL image de couverture')->onlyOnForms(),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters

            ->add(EntityFilter::new('accomodation', 'Logement'))
            ->add(NumericFilter::new('nbPlace', 'Nombre de résidents'))
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
