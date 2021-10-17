<?php

namespace App\Form;

use App\Entity\Recette;
use App\Entity\Ingredients;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Repository\IngredientsRepository;

use Symfony\Component\OptionsResolver\OptionsResolver;

class Recette1Type extends AbstractType
{
    private $ingredients;

    

    public function __construct(IngredientsRepository $ingredientsRepository)
    {
        $this->ingredients=$ingredientsRepository->findAll();
        // dd($this->ingredients->getTitre());
        
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('sousTitre')
            ->add('ingredients',EntityType::class,[
                'class'=>Ingredients::class,
                 'choice_label' => 'titre',
                 'multiple' => true,
                 'expanded' => true,
                 'choices' =>$this->ingredients,
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recette::class,
        ]);
    }
}
