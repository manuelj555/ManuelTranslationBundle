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
     * @var TranslationRepository
     */
    protected $translationRepository;

    function __construct($translationRepository)
    {
        $this->translationRepository = $translationRepository;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'manuel_translation';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('values', 'collection', array(
            'type' => 'textarea',
        ));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'addCodeAndDomainForms'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ManuelAguirre\Bundle\TranslationBundle\Entity\Translation',
//            'error_bubbling' => true,
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