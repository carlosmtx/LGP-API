<?php
/**
 * User: carlos
 * Date: 09/03/2015
 * Time: 21:52
 */

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ChannelType extends AbstractType{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text');
    }

    public function getName()
    {
        return 'channel_form_type';
    }
}