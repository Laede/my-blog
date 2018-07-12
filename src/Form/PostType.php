<?php

namespace App\Form;


use App\Entity\Labels;
use App\Entity\Post;

use App\Form\DataTransformer\StringToArrayTransformer;
use App\Repository\LabelsRepository;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    /**
     * @var StringToArrayTransformer
     */
    private $transformer;
    /**
     * @var LabelsRepository
     */
    private $labelsRepository;

    public function __construct(StringToArrayTransformer $transformer, LabelsRepository $labelsRepository)
    {

        $this->transformer = $transformer;
        $this->labelsRepository = $labelsRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('label',TextType::class,[
                'attr' => [
                    'data-role' => 'my_tags',
                    'data-items' => $this->getLabels()
                ],
            ])
            ->add('postBody', CKEditorType::class,[
                'config' =>[
                    'filebrowserUploadRoute' => 'uploadImage',
                    'uiColor' => '#ffffff'
                ]
            ])
        ;
        $builder->get('label')
            ->addModelTransformer($this->transformer)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }

    public function getLabels()
    {
        $array = [];
        $labels = $this->labelsRepository->findAll();
        foreach ($labels as $label){
            $array[] = $label->getName();
        }
        return json_encode($array);
    }
}
