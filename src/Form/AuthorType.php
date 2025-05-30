<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class AuthorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', type:TextType::class)
            ->add('nationality', TextType::class, [
                 'required' => false,
            ])              
            ->add('dateOfBirth', DateType::class, [
                'input' => 'datetime_immutable',
                'widget' => 'single_text',
            ])
            ->add('dateOfDeath', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'required' => false,
            ])
           ->add('books', EntityType::class, [
                'class' => Book::class,
                'choice_label' => 'title',
                'multiple' => true,
                'required' => false,
                'by_reference' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Author::class,
        ]);
    }
}
