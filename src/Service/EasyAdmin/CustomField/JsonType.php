<?php

namespace App\Service\EasyAdmin\CustomField;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Traversable;

class JsonType extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $valueObject = 'App\ValueObject\\' . $builder->getName();

        foreach ($valueObject::getAdminConfig() as $field) {
            $builder->add($field['name'], $field['type'], $field['options']);
        }

        $builder->setDataMapper($this);
    }

    public function mapDataToForms(mixed $viewData, Traversable $forms): void
    {
        if (null === $viewData) {
            return;
        }

        $valueObjectArray = $viewData->toArray();

        foreach ($forms as $form) {
            $form->setData($valueObjectArray[$form->getName()]);
        }
    }

    public function mapFormsToData(Traversable $forms, mixed &$viewData): void
    {
        $data = [];
        foreach ($forms as $form) {
            $data[$form->getName()] = (int) $form->getData();
        }

        $viewData->fromArray($data);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('empty_data', null);
    }
}
