<?php

namespace App\Form;

use App\Entity\Booking;
use App\form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\DataTransformer\FrToDatetimeTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BookingType extends ApplicationType
{

    private $tranformer;

    public function __construct(FrToDatetimeTransformer $transformer){
        $this->transformer = $transformer;

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate',TextType::class,$this->getConfiguration("Date d'arrivée","La date de votre arrivée"))
            ->add('endDate',TextType::class,$this->getConfiguration("Date du départ","La date de votre départ"))
            ->add('comment',TextareaType::class,$this->getConfiguration(false,"Ajoutez un commentaire sur le lieu",['required'=>false]))
        ;

        $builder->get('startDate')->addModelTransformer($this->transformer);
        $builder->get('endDate')->addModelTransformer($this->transformer);

        // ->add('startDate', DateType::class, [
        //     'label' => 'Date de début',
        //     'widget' => 'single_text',
        //     'html5' => false,
        //     'attr' => ['class' => 'js-datepicker'],
        //     'format' => 'dd/MM/yyyy',
        //     'input' => 'datetime',
        // ])
        // ->add('endDate', DateType::class, [
        //     'label' => 'Date de début',
        //     'widget' => 'single_text',
        //     'html5' => false,
        //     'attr' => ['class' => 'js-datepicker'],
        //     'format' => 'dd/MM/yyyy',
        //     'input' => 'datetime',
        // ])
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
