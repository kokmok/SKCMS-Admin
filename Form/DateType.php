<?php

namespace SKCMS\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class DateType extends AbstractType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'skscms_date';
    }
    
    public function getParent()
    {
        return 'text';
    }
}
