<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('firstname')
            ->add('lastname')
            ->add('thumbnail', FileType::class, [
                'required' => false,
                'label' => 'Change profil picture (optional)',
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
		->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
			'csrf_protection' => true,
			// the name of the hidden HTML field that stores the token
			'csrf_field_name' => '_token',
			// an arbitrary string used to generate the value of the token
			// using a different string for each form improves its security
			'csrf_token_id'   => 'task_item',
        ]);
	}  
}
