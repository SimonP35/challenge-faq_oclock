<?php

namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('body')
            ->add('tags', null, [
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('image', FileType::class, [
                'label' => '(Optionnel) Partager une image illustrant votre question',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'minWidth' => 100,
                        'maxWidth' => 600,
                        'minHeight' => 100,
                        'maxHeight' => 400,
                    ]),
                    new File([
                        'maxSize' => '2M', 
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ],
        ]);
    }
}
