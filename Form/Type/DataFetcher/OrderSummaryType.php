<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReportBundle\Form\Type\DataFetcher;

use Symfony\Component\Form\AbstractType;
use Sylius\Bundle\ReportBundle\DataFetcher\OrderSummaryDataFetcher;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class OrderSummaryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start', DateType::class, [
                'label' => 'sylius.form.report.user_registration.start',
                'years' => range(date('Y') - 100, date('Y')),
                'widget' => 'single_text',
            ])
            ->add('end', DateType::class, [
                'label' => 'sylius.form.report.user_registration.end',
                'years' => range(date('Y') - 100, date('Y')+20),
                'widget' => 'single_text',
            ])
            ->add('period', ChoiceType::class, [
                'choices' => OrderSummaryDataFetcher::getPeriodChoices(),
                'multiple' => false,
                'label' => 'sylius.form.report.user_registration.period',
            ])
            ->add('empty_records', CheckboxType::class, [
                'label' => 'sylius.form.report.user_registration.empty_records',
                'required' => false,
            ])
        ;
    }
}
