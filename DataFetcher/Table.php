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

use Sylius\Component\Report\DataFetcher\Data;
use Sylius\Component\Report\DataFetcher\DataFetcherInterface;

/**
 * Abstract class to provide time periods logic.
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
abstract class Table implements DataFetcherInterface
{
    const PERIOD_DAY = 'day';
    const PERIOD_WEEK = 'week';
    const PERIOD_MONTH = 'month';
    const PERIOD_YEAR = 'year';

    /**
     * @return array
     */
    public static function getPeriodChoices()
    {
        return [
            'Daily' => self::PERIOD_DAY,
            'Weekly' => self::PERIOD_WEEK,
            'Monthly' => self::PERIOD_MONTH,
            'Yearly' => self::PERIOD_YEAR,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(array $configuration)
    {
        $data = new Data();

        $rawData = $this->getData($configuration);

        if (empty($rawData)) {
            return $data;
        }
        
        $labels = array_keys($rawData[0]);
        
        $data->setLabels($labels);
        
        $fetched = [];
        
        foreach($rawData as $d){
            $f = [];
            foreach($labels as $label){
                if ($d[$label] instanceof \DateTime){
                    $f[$label] = $d[$label]->format("Y-m-d");
                }
                else{
                    $f[$label] = $d[$label];
                }
            }
            $fetched[] = $f;
        }
        
        // var_dump($fetched);
        // die();
        
        $data->setData($fetched);

        return $data;
    }

    /**
     * Responsible for providing raw data to fetch, from the configuration (ie: start date, end date, time period,
     * empty records flag, interval, period format, presentation format, group by).
     *
     * @param array $configuration
     *
     * @return array
     */
    abstract protected function getData(array $configuration = []);

    /**
     * @param array  $configuration
     * @param string $interval
     * @param string $periodFormat
     * @param string $presentationFormat
     * @param array  $groupBy
     */
    private function setExtraConfiguration(
        array &$configuration,
        $interval,
        $periodFormat,
        $presentationFormat,
        array $groupBy
    ) {
        $configuration['interval'] = $interval;
        $configuration['periodFormat'] = $periodFormat;
        $configuration['presentationFormat'] = $presentationFormat;
        $configuration['groupBy'] = $groupBy;
    }

    /**
     * @param array $fetched
     * @param array $configuration
     *
     * @return array
     */
    private function fillEmptyRecords(array $fetched, array $configuration)
    {
        $date = $configuration['start'];
        $dateInterval = new \DateInterval($configuration['interval']);

        $numberOfPeriods = $configuration['start']->diff($configuration['end']);
        $formattedNumberOfPeriods = $numberOfPeriods->format($configuration['periodFormat']);

        for ($i = 0; $i <= $formattedNumberOfPeriods; ++$i) {
            $fetched[$date->format($configuration['presentationFormat'])] = 0;
            $date = $date->add($dateInterval);
        }

        return $fetched;
    }
}
