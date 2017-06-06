<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 2017-03-16
 * Time: 17:12
 */

namespace AppBundle\Form;

use Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class)
            ->add('author',DocumentType::class, array(
                'class'=>'AppBundle\Document\Author',
                'multiple'=>false
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'=>'AppBundle\Document\Book',
            'csrf_protection'=>'false',
        ));
    }
}