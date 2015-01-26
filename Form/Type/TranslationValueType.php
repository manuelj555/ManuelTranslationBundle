<?php
/*
 * This file is part of the Manuel Aguirre Project.
 *
 * (c) Manuel Aguirre <programador.manuel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ManuelAguirre\Bundle\TranslationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


/**
 * @autor Manuel Aguirre <programador.manuel@gmail.com>
 */
class TranslationValueType extends AbstractType
{
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'translation_value';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value');

        $builder->addEventListener(FormEvents::SUBMIT, array($this, 'setLocale'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ManuelAguirre\Bundle\TranslationBundle\Entity\TranslationValue',
        ));
    }

    public function setLocale(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if(null === $data->getLocale()){
            $data->setLocale($form->getName());
        }
    }
}