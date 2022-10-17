<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryCrudController extends AbstractCrudController
{

    private SluggerInterface $slug;

    public function __construct(SluggerInterface $slug) {
        $this->slug = $slug;
    }

    public static function getEntityFqcn(): string
    {
        return Category::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new("id")->hideOnForm(),
            TextField::new("title"),
            TextField::new('slug')->hideOnForm()->hideOnIndex()
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Category) return;
        $entityInstance->setSlug($this->slug->slug($entityInstance->getTitle())->lower());

        parent::persistEntity($entityManager, $entityInstance);


    }

    public function configureCrud(Crud $crud): Crud
    {

        $crud->setDefaultSort(["title" => "ASC"]);

        return $crud;
    }


}
