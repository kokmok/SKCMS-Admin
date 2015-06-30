<?php

namespace SKCMS\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class DatetimeType extends AbstractType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'skscms_datetime';
    }
    
    public function getParent()
    {
        return 'text';
    }
}
