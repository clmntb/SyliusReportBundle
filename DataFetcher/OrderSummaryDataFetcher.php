<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReportBundle\DataFetcher;

use Sylius\Bundle\ReportBundle\Doctrine\ORM\OrderRepositoryInterface;
use Sylius\Bundle\ReportBundle\Form\Type\DataFetcher\OrderSummaryType;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class OrderSummaryDataFetcher extends Table
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct($productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function getData(array $configuration = [])
    {
        return $this->productRepository->productPerformanceSummary($configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return OrderSummaryType::class;
    }
}
