<?php

namespace Display\CmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MenuType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('locale', 'locale', array(
                'label' => 'Locale'
            ))
            ->add('menus', 'hidden', array(
                'attr' => array('class' => 'pages')
            ))
        ;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'display_cms_menu';
    }
}
