<?php

namespace Puzzle\ExpertiseBundle\Form\Model;

use Puzzle\ExpertiseBundle\Entity\Testimonial;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class AbstractTestimonialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder->add('author', TextType::class)
                ->add('company', TextType::class, ['required' => false])
                ->add('position', TextType::class, ['required' => false])
                ->add('picture', HiddenType::class, [
                    'required' => false,
                    'mapped' => false
                ])
                ->add('message', TextareaType::class, ['required' => false])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Testimonial::class,
            'validation_groups' => array(
                Testimonial::class,
                'determineValidationGroups',
            ),
        ));
    }
}