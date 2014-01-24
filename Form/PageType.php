<?php

namespace Display\CmsBundle\Form;

use Display\CmsBundle\Entity\PageRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PageType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('slug')
            ->add('cacheable', 'choice', array(
                'choices' => array('No', 'Yes')
            ))
            ->add('status', 'choice', array(
                'choices' => PageRepository::getAvailableStatus()
            ))
            ->add('parent')
            ->add('locale')
            ->add('title')
            ->add('headerTitle')
            ->add('description', 'textarea', array('attr' => array('rows' => 10)))
            ->add('content', 'textarea', array('attr' => array('rows' => 25, 'class' => 'markitup')))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Display\CmsBundle\Entity\Page'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'display_cms_page';
    }
}
