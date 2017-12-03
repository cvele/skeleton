<?php

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ChangePasswordType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $constraintsOptions = [
            'message' => 'form.user.change_password.invalid_current_password',
        ];

        $builder
        ->add('current_password', PasswordType::class, [
            'label' => 'form.user.change_password.current_password',
            'mapped' => false,
            'constraints' => [
                new NotBlank(),
                new UserPassword($constraintsOptions),
            ],
        ])
        ->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'first_options' => ['label' => 'form.user.change_password.new_password'],
            'second_options' => ['label' => 'form.user.change_password.new_password_repeat'],
            'invalid_message' => 'form.user.change_password.password_mismatch',
        ])
        ->add('save', SubmitType::class, [
            'label' => 'form.user.change_password.submit'
        ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_token_id' => 'change_password',
        ]);
    }
}
