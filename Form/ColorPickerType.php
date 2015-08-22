<?php

namespace SKCMS\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class ColorPickerType extends AbstractType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'skcms_colorpicker';
    }
    
    public function getParent()
    {
        return 'text';
    }
}
