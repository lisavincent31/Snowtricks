<?php

namespace App\Form;

use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            // On ajoute le champ "images" dans le formulaire
            // Il n'est pas lié à la base de données (mapped à false)
            ->add('images', FileType::class,[
                'label' => 'Ajouter d\'autres images :',
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ])
            ->add('videos', TextType::class, [
                'label' => 'Ajouter l\'URL d\'une vidéo :',
                'required' => false,
                'mapped' => false
            ])
            ->add('featured_image', FileType::class,[
                'label' => 'Image à la une :',
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
        ]);
    }
}