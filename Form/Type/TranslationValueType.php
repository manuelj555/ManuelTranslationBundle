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
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @autor Manuel Aguirre <programador.manuel@gmail.com>
 */
class TranslationValueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value');

        $builder->addEventListener(FormEvents::SUBMIT, array($this, 'setLocale'));
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => TranslationValueType::class,
        ));
    }

    public function setLocale(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (null === $data->getLocale()) {
            $data->setLocale($form->getName());
        }
    }
}