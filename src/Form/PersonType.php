<?php

namespace App\Form;

use App\Entity\Person;
use App\Entity\Sector;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PersonType extends AbstractType
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $baseChoices = $this->entityManager->getRepository(Sector::class)->findBy([], ["seq" => "ASC"]);

        $builder
            ->add('name', TextType::class, [
                'label' => "Name",
                'required' => false,
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 1])
                ]
            ])
            ->add('sectors', ChoiceType::class, [
                'label' => "Sectors",
                'choices' => $this->createSpacingForSectorChoices($baseChoices),
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'size' => 15
                ],
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('agreed_to_terms', CheckboxType::class, [
                'label' => "Agree to terms",
                'required' => false,
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => "Save",
            ]);
    }

    private function createSpacingForSectorChoices($baseChoices)
    {
        $choices = [];
        /** @var Sector $choice */
        foreach ($baseChoices as $choice) {
            $spaces = "";
            $nbsp = html_entity_decode('&nbsp;');
            for ($i = $this->getSpacesAmount($choice); $i > 0; $i--) {
                $spaces .= $nbsp . $nbsp . $nbsp . $nbsp;
            }
            $choices[$spaces . $choice->getName()] = $choice->getId();
        }

        return $choices;
    }

    private function getSpacesAmount(Sector $sector)
    {
        if (!$sector->getParent()) {
            return 0;
        }

        $count = 1;
        $count += $this->getSpacesAmount($sector->getParent());

        return $count;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Person::class,
        ]);
    }
}