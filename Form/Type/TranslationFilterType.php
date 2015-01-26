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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


/**
 * @autor Manuel Aguirre <programador.manuel@gmail.com>
 */
class TranslationFilterType extends AbstractType
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
        return 'translation_filter';
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
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data' => array(
                'domains' => array('messages'),
            ),
            'translation_domain' => 'ManuelTranslationBundle',
        ));
    }


}