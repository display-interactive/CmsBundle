<?php

namespace Display\CmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FragmentType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('cacheable', 'choice', array(
                'choices' => array('No', 'Yes')
            ))
            ->add('content', 'textarea', array('attr' => array('rows' => 25, 'class' => 'markitup')))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Display\CmsBundle\Entity\Fragment'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'display_cms_fragment';
    }
}
