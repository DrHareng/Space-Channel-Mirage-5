<?php

namespace App\Form;

use App\Entity\Photo;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Vich\UploaderBundle\Form\Type\VichFileType;

class PhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'photo.title',
                'attr' => [
                    'placeholder' => 'photo.title_placeholder',
                    'class' => 'form-control'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'photo.description',
                'required' => false,
                'attr' => [
                    'placeholder' => 'photo.description_placeholder',
                    'rows' => 4,
                    'class' => 'form-control'
                ]
            ])
            ->add('imageFile', VichFileType::class, [
                'label' => 'photo.image_file',
                'required' => true,
                'allow_delete' => false,
                'download_uri' => false,
                'attr' => [
                    'accept' => 'image/*',
                    'class' => 'form-control'
                ]
            ])
            ->add('tags', EntityType::class, [
                'label' => 'photo.tags',
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Photo::class,
        ]);
    }
}
