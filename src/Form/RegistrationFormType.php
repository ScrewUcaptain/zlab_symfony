<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('email')
			->add('firstname')
			->add('lastname')
			->add('thumbnail', FileType::class, [
				'required' => false,
				'label' => 'Choose a profil picture (optional)',
				'mapped' => false,
				"attr" => [
					'class' => 'custom-file-upload',
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
			//->add('agreeTerms', CheckboxType::class, [
			//	'mapped' => false,
			//	'constraints' => [
			//		new IsTrue([
			//			'message' => 'You should agree to our terms.',
			//		]),
			//	],
			//])
			->add('password', RepeatedType::class, [
				'type' => PasswordType::class,
				'invalid_message' => 'The password fields must match.',
				'options' => ['attr' => ['class' => 'password-field']],
				'required' => true,
				'first_options'  => ['label' => 'Password'],
				'second_options' => ['label' => 'Repeat Password'],
				'constraints' => [
					new NotBlank([
						'message' => 'Please enter a password',
					]),
					new Length([
						'min' => 6,
						'minMessage' => 'Your password should be at least {{ limit }} characters',
						// max length allowed by Symfony for security reasons
						'max' => 4096,
					]),
				],
			]);
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
