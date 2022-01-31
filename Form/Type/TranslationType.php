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

use ManuelAguirre\Bundle\TranslationBundle\Entity\Translation;
use ManuelAguirre\Bundle\TranslationBundle\Entity\TranslationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @autor Manuel Aguirre <programador.manuel@gmail.com>
 */
class TranslationType extends AbstractType
{
    public function __construct(
        private TranslationRepository $translationRepository,
        private array $activeLocales,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('values', 'collection', array(
            'type' => 'textarea',
        ));


        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'addCodeAndDomainForms'));

        $builder->get('values')->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();
            $data = array_replace(array_fill_keys($this->activeLocales, null), $data);
            $event->setData($data);
        }, 1000);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Translation::class,
            'translation_domain' => 'ManuelTranslationBundle',
        ));
    }

    public function addCodeAndDomainForms(FormEvent $event)
    {
        $form = $event->getForm();

        $disabled = ($event->getData() and $event->getData()->getId());

        $form->add('code', null, array(
            'disabled' => $disabled,
            'label' => 'label.code',
        ));
        $form->add('domain', null, array(
            'disabled' => $disabled,
            'label' => 'label.domain',
            'data' => 'messages',
        ));

        $domains = $this->translationRepository->getExistentDomains();
        $domains['messages'] = 'messages';

        $form->add('existent_domains', 'choice', array(
            'disabled' => $disabled,
            'label' => false,
            'choices' => $domains,
            'expanded' => true,
            'multiple' => false,
            'required' => false,
            'mapped' => false,
            'placeholder' => false,
        ));
    }
}