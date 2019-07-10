<?php

namespace Puzzle\ExpertiseBundle\Form\Model;

use Puzzle\ExpertiseBundle\Entity\Project;
use Puzzle\ExpertiseBundle\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class AbstractProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder->add('name', TextType::class)
                ->add('shortDescription', TextType::class, ['required' => false])
                ->add('description', TextareaType::class, ['required' => false])
                ->add('service', EntityType::class, [
                    'class' => Service::class,
                    'choice_label' => 'name'
                ])
                ->add('startedAt', TextType::class, ['required' => false])
                ->add('endedAt', TextType::class, ['required' => false])
                ->add('client', TextType::class, ['required' => false])
                ->add('location', TextType::class, ['required' => false])
                ->add('pictures', HiddenType::class, ['required' => false,'mapped' => false])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Project::class,
            'validation_groups' => array(
                Project::class,
                'determineValidationGroups',
            ),
        ));
    }
}