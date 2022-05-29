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

use ManuelAguirre\Bundle\TranslationBundle\Entity\TranslationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @autor Manuel Aguirre <programador.manuel@gmail.com>
 */
class TranslationFilterType extends AbstractType
{
    function __construct(private TranslationRepository $translationRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('search', 'text', array(
                'required' => false,
                'label' => 'label.search',
            ));

        $domains['messages'] = 'messages';

        $domains = $domains + $this->translationRepository->getExistentDomains();

        $builder->add('domains', 'choice', array(
            'choices' => $domains,
            'expanded' => true,
            'multiple' => true,
            'required' => false,
        ));

        $builder->add('inactive', 'checkbox', array(
            'required' => false,
            'label' => 'label.inactives',
        ));
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data' => array(
                'domains' => array('messages'),
            ),
            'translation_domain' => 'ManuelTranslationBundle',
        ));
    }

}