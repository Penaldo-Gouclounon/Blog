<?php
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ProductType
{
    // ...

    $builder->add('products', EntityType::class, [
        // looks for choices from this entity
        'class' => Product::class,

        // uses the User.username property as the visible option string
        'choice_label' => 'username',

        // used to render a select box, check boxes or radios
        // 'multiple' => true,
        // 'expanded' => true,
    ]);
}