<?php
/**
 * @author Manuel Aguirre
 */

namespace ManuelAguirre\Bundle\TranslationBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Translatable\Entity\Repository\TranslationRepository;
use Gedmo\Translatable\Entity\Translation;
use Gedmo\Translatable\TranslatableListener;
use ManuelAguirre\Bundle\TranslationBundle\Doctrine\Translatable\TranslatableEntitiesProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Manuel Aguirre
 */
#[Route("/doctrine")]
class DoctrineExtensionController extends AbstractController
{
    #[Route("/", name: "manuel_translation_doctrine_index")]
    public function index(TranslatableEntitiesProvider $provider): Response
    {
        $entities = $provider->getEntities();
        $form = $this->createEntityForm($entities);

        return $this->render('@ManuelTranslation/Doctrine/Extension/index.html.twig', [
            'entities' => $entities,
            'form' => $form->createView(),
        ]);
    }

    #[Route("/edit", name: "manuel_translation_doctrine_edit")]
    public function edit(
        Request $request,
        TranslatableEntitiesProvider $provider,
        EntityManagerInterface $entityManager,
        TranslatableListener $translatableListener
    ): Response {
        $entities = $provider->getEntities();
        $form = $this->createEntityForm($entities);
        $form->handleRequest($request);
        $data = $form->getData();

        /** @var TranslationRepository $rep */
        $rep = $entityManager->getRepository(Translation::class);
        $entity = $entityManager->getRepository($data['entity'])->find($data['identifiers']);

        return $this->render('@ManuelTranslation/Doctrine/Extension/index.html.twig', [
            'entities' => $entities,
            'form' => $form->createView(),
        ]);
    }

    private function createEntityForm(array $entities): \Symfony\Component\Form\FormInterface
    {
        return $this->createFormBuilder(null, [
            'method' => 'get',
            'csrf_protection' => false,
            'action' => $this->generateUrl('manuel_translation_doctrine_edit')
        ])
            ->add('entity', ChoiceType::class, [
                'choices' => array_combine($entities, $entities),
            ])
            ->add('identifiers', TextType::class)
            ->add('Buscar', SubmitType::class)
            ->getForm();
    }
}