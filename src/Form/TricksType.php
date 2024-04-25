<?php

namespace App\Form;

use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

class TricksType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la figure :',
                'required' => true
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Catégorie :',
                'choices' => [
                    'Grab' => 'grab',
                    'Rotation' => 'rotation',
                    'Slide' => 'slide',
                    'Flip' => 'flip'
                ],
                'required' => true
            ])
            ->add('content', TextType::class, [
                'label' => 'Description la figure :',
                'required' => true
            ])
            ->add('media', FileType::class,[
                'label' => false,
                'multiple' => false,
                'mapped' => false,
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
            'allow_extra_fields' => true,
            'constraints' => [
                new UniqueEntity(['fields' => ['name']]),
            ],
        ]);
    }
}