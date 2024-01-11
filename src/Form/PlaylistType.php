<?php

namespace App\Form;

use App\Entity\Tag;
use App\Entity\Song;
use App\Entity\Playlist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PlaylistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('isPublic', ChoiceType::class, [
                'choices' => [
                    'Public' => true,
                    'Private' => false,
                ],
            ])
            ->add('cover', FileType::class, [
				'required' => false,
				'label' => 'Choose a cover image (optional)',
				'mapped' => false,
				"attr" => [
					'class' => 'custom-file-upload'
				],
				'constraints' => [
					new File([
						'maxSize' => '2024k',
						'mimeTypes' => [
							'image/*'
						],
						'mimeTypesMessage' => 'Please upload a valid image',
					])
				]
			])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true,
			'expanded' => false,
		])
            ->add('songs', EntityType::class, [
                'class' => Song::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
            ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Playlist::class,
        ]);
    }
}
