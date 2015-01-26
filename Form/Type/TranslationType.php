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
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


/**
 * @autor Manuel Aguirre <programador.manuel@gmail.com>
 */
class TranslationType extends AbstractType
{
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'translation';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        $builder->add('code', null, array('error_bubbling' => true));
//        $builder->add('domain', null, array('error_bubbling' => true));
        $builder->add('values', 'collection', array(
            'type' => new TranslationValueType(),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'error_bubbling' => true,
        ));
        $builder->add('localEditions', 'hidden', array('error_bubbling' => true));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'addCodeAndDomainForms'));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'validateLocalEditions'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ManuelAguirre\Bundle\TranslationBundle\Entity\Translation',
            'error_bubbling' => true,
            'translation_domain' => 'ManuelTranslationBundle',
        ));
    }

    public function addCodeAndDomainForms(FormEvent $event)
    {
        $form = $event->getForm();

        $disabled = $event->getData()->getId() != null;

        $form->add('code', null, array(
            'error_bubbling' => true,
            'disabled' => $disabled,
            'label' => 'label.code',
        ));
        $form->add('domain', null, array(
            'error_bubbling' => true,
            'disabled' => $disabled,
            'label' => 'label.domain',
        ));
    }

    public function validateLocalEditions(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        dump($data, $form['localEditions']->getData());

        if($data['localEditions'] < $form['localEditions']->getData()){
            //si es menor, significa que otra persona ha hecho cambios
            $form->addError(new FormError("Please Refresh Page"));
        }
    }
}