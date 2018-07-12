<?php

namespace App\Form\DataTransformer;

use App\Entity\Labels;
use App\Repository\LabelsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class StringToArrayTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var LabelsRepository
     */
    private $labelsRepository;

    public function __construct(EntityManagerInterface $entityManager, LabelsRepository $labelsRepository)
    {

        $this->entityManager = $entityManager;
        $this->labelsRepository = $labelsRepository;
    }
    
    public function transform($value)
    {
        $temp = [];
        foreach ($value as $element){
            $temp[] = $element->getName();
        }
        return implode(',', $temp);
    }

    public function reverseTransform($value)
    {
        $temp = [];
        $array = explode(',', $value);
        foreach ($array as $element)
        {
            if(count($this->labelsRepository->findBy(['name' => $element])) == 0 ){
                $object = $element;
                $object = new Labels();
                $object->setName($element);
                $this->entityManager->persist($object);
                $this->entityManager->flush();
            }
            $object = $this->labelsRepository->findBy(['name' => $element]);
            $temp[] = $object[0];
        }
        return $temp;
    }
}