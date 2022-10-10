<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\DBAL\Types\TextType;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextareaType::class)
            ->add('content', TextareaType::class)
            ->add("category", EntityType::class, [
                "class" => Category::class,
                "choice_label" => "title",
                "required" => false,
                "placeholder" => "No category"
            ])
            ->add("isPublished", CheckboxType::class, [
                "required" => false
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
