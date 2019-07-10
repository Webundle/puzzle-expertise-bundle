<?php

namespace Puzzle\ExpertiseBundle\Form\Type;

use Puzzle\ExpertiseBundle\Form\Model\AbstractProjectType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class ProjectUpdateGalleryType extends AbstractProjectType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        parent::buildForm($builder, $options);
        
        $builder->remove('name')
                ->remove('description')
                ->remove('shortDescription')
                ->remove('service')
                ->remove('startedAt')
                ->remove('endedAt')
                ->remove('client')
                ->remove('location');
    }
    
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        
        $resolver->setDefault('csrf_token_id', 'project_update_gallery');
        $resolver->setDefault('validation_groups', ['Update']);
    }
    
    public function getBlockPrefix() {
        return 'puzzle_admin_expertise_project_update_gallery';
    }
}