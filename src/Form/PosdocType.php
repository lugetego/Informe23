<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PosdocType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('programasp', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType',array(
                'label'=>'*Programa',
                'choices'=>array(
                    'DGAPA'=>'DGAPA',
                    'Conacyt'=>'Conacyt',
                    'Otro'=>'Otro',
                ),
                // *this line is important*
                'placeholder' => 'Seleccionar',
                'mapped'=> false,
                'required'=>true,
            ))

            ->add('nombre', 'Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'label'=>'*Nombre',
                'required'=>true,
            ))
            ->add('inicio','Symfony\Component\Form\Extension\Core\Type\DateType',array(
                'placeholder' => array(
                    'year' => 'Año',
                    'month' => 'Mes',
                    'day' => 'Día'),
                'years'=> range(2021,2023),
                'label'=>'*Fecha de inicio',
                'required'=>true,

            ))
            ->add('fin','Symfony\Component\Form\Extension\Core\Type\DateType',array(
                'placeholder' => array(
                    'year' => 'Año',
                    'month' => 'Mes',
                    'day' => 'Día'),
                'years'=> range(2021,2024),
                'label'=>'*Fecha de término',
                'required'=>true,
            ))

            ->add('programa',TextType::class,
                array('label' => 'Otro programa',
                    'attr' => ['read_only'=> true],
                ))
        ;

        $formModifier = function (FormInterface $form, $otro) {

            if ( 'Otro' == $otro) {
                $form->add('programa', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                    'label' => 'Programa',
                ));
            }
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                // this would be your entity, i.e. SportMeetup
                $data = $event->getData();
                $formModifier($event->getForm(), $data->getPrograma());
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                // this would be your entity, i.e. SportMeetup

                $data = $event->getData();
                if (isset($data['programasp'])){

                    $val = $data['programasp'];
                    if ( $val !='Otro') {
                        $data['programa'] = $val;
                        $event->setData($data);
                    }
                }
                else {
                    $data['programasp']='';
                }
            }
        );
        $builder->get('programasp')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $sport = $event->getForm()->getData();

                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!

                $formModifier($event->getForm()->getParent(),$sport);
            }
        );


    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Posdoc'
        ));
    }


}
