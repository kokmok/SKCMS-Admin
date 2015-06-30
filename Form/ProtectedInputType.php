<?php

namespace SKCMS\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class ProtectedInputType extends AbstractType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'skscms_protecedinput';
    }
    
    public function getParent()
    {
        return 'text';
    }
}
