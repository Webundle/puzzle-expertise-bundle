<?php

namespace Puzzle\ExpertiseBundle\Form\Model;

use Puzzle\ExpertiseBundle\Entity\Service;
use Puzzle\ExpertiseBundle\Entity\Staff;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class AbstractStaffType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder->add('firstName', TextType::class)
                ->add('lastName', TextType::class)
                ->add('service', EntityType::class, [
                    'class' => Service::class,
                    'choice_label' => 'name',
                ])
                ->add('position', TextType::class, ['required' => false])
                ->add('email', EmailType::class, ['required' => false])
                ->add('phoneNumber', TextType::class, ['required' => false])
                ->add('facebook', TextType::class, ['required' => false])
                ->add('twitter', TextType::class, ['required' => false])
                ->add('linkedIn', TextType::class, ['required' => false])
                ->add('picture', HiddenType::class, [
                    'required' => false,
                    'mapped' => false
                ])
                ->add('description', TextareaType::class, ['required' => false])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => Staff::class,
            'validation_groups' => array(
                Staff::class,
                'determineValidationGroups',
            ),
        ));
    }
}