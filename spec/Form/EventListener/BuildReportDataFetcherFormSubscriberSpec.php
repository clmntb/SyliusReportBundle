<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ReportBundle\Form\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ReportBundle\Form\EventListener\BuildReportDataFetcherFormSubscriber;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Report\DataFetcher\DataFetcherInterface;
use Sylius\Component\Report\Model\ReportInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class BuildReportDataFetcherFormSubscriberSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(BuildReportDataFetcherFormSubscriber::class);
    }

    public function it_implements_data_fetcher_interface()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    public function let(ServiceRegistryInterface $dataFetcherRegistry, FormFactoryInterface $factory, DataFetcherInterface $dataFetcher)
    {
        $dataFetcherRegistry->get('test_data_fetcher')->willReturn($dataFetcher);
        $dataFetcher->getType()->willReturn('sylius_data_fetcher_test_type');

        $this->beConstructedWith($dataFetcherRegistry, $factory);
    }

    public function it_adds_configuration_fields_in_pre_set_data(
        $factory,
        ReportInterface $report,
        FormEvent $event,
        Form $form,
        Form $field)
    {
        $report->getDataFetcher()->willReturn('test_data_fetcher');
        $report->getDataFetcherConfiguration()->willReturn([]);

        $event->getData()->willReturn($report);
        $event->getForm()->willReturn($form);

        $factory->createNamed(
            'dataFetcherConfiguration',
            'sylius_data_fetcher_test_type',
            Argument::cetera()
        )->willReturn($field);

        $form->add($field)->shouldBeCalled();

        $this->preSetData($event);
    }

    public function it_adds_configuration_fields_in_pre_bind(
        $factory,
        FormEvent $event,
        Form $form,
        Form $field)
    {
        $data = ['dataFetcher' => 'test_data_fetcher'];

        $event->getData()->willReturn($data);
        $event->getForm()->willReturn($form);

        $factory->createNamed(
            'dataFetcherConfiguration',
            'sylius_data_fetcher_test_type',
            Argument::cetera()
        )->willReturn($field);

        $form->add($field)->shouldBeCalled();

        $this->preBind($event);
    }

    public function it_does_not_allow_to_confidure_fields_in_pre_set_data_for_other_class_then_report(FormEvent $event)
    {
        $report = '';
        $event->getData()->willReturn($report);
        $this->shouldThrow(new UnexpectedTypeException($report, ReportInterface::class))
            ->duringPreSetData($event);
    }
}
