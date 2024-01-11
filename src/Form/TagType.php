<?php

namespace App\Form;

use App\Entity\Tag;
use App\Entity\Playlist;
use PHPUnit\Util\Color;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;

class TagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('color', ColorType::class)
            // ->add('playlists', EntityType::class, [
            //     'class' => Playlist::class,
            //     'choice_label' => 'name',
            //     'multiple' => true,
            // ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tag::class,
			'csrf_protection' => true,
			// the name of the hidden HTML field that stores the token
			'csrf_field_name' => '_token',
			// an arbitrary string used to generate the value of the token
			// using a different string for each form improves its security
			'csrf_token_id'   => 'task_item',
        ]);
    }
}
