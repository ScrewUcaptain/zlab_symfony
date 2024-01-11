<?php

namespace App\Form;

use App\Entity\Song;
use App\Entity\Playlist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SongType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('url')
            ->add('year')
            ->add('artist')
            ->add('playlists', EntityType::class, [
                'class' => Playlist::class,
                'choice_label' => 'id',
                'multiple' => true,
                'required' =>  false,
            ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Song::class,
			'csrf_protection' => true,
			// the name of the hidden HTML field that stores the token
			'csrf_field_name' => '_token',
			// an arbitrary string used to generate the value of the token
			// using a different string for each form improves its security
			'csrf_token_id'   => 'task_item',
        ]);
    }
}
