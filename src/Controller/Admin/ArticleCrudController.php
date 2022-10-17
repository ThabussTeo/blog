<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleCrudController extends AbstractCrudController
{

    private SluggerInterface $slug;

    public function __construct(SluggerInterface $slug) {
        $this->slug = $slug;
    }

    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new("id")->hideOnForm(),
            TextField::new("title"),
            TextEditorField::new("content")->setSortable(false)->hideOnIndex(),
            AssociationField::new("category"),
            DateTimeField::new("createdAt")->hideOnForm()->setLabel("Creation date"),
            TextField::new('slug')->hideOnForm(),
            BooleanField::new("isPublished")
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Article) return;
        $entityInstance->setCreatedAt(new \DateTime())
                       ->setSlug($this->slug->slug($entityInstance->getTitle())->lower());

        parent::persistEntity($entityManager, $entityInstance);


    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud->setPageTitle(Crud::PAGE_INDEX, "Article list")
             ->setPageTitle(Crud::PAGE_NEW, "Add an article")
             ->setPageTitle(Crud::PAGE_EDIT, "Edit an article")
             ->setPaginatorPageSize(10)
             ->setDefaultSort(["createdAt" => "DESC"]);
        return $crud;
    }

    public function configureActions(Actions $actions): Actions
    {

        $actions->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
            return $action->setLabel("Add an Article")
                   ->setIcon("fa fa-plus");
        });

        $actions->update(Crud::PAGE_NEW, Action::SAVE_AND_RETURN, function (Action $action) {
            return $action->setLabel("Submit")
                          ->setIcon("fa fa-check");
        });

        $actions->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER);


        $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
        $actions->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {
           return $action->setLabel("Show article details");
        });

        return $actions;
    }

    public function configureFilters(Filters $filters): Filters
    {
        $filters->add("title");
        $filters->add("createdAt");
        return $filters;
    }


}
