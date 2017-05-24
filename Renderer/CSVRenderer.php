<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReportBundle\Renderer;

use Port\Csv\CsvWriter;
use Sylius\Bundle\ReportBundle\Form\Type\Renderer\CSVConfigurationType;
use Sylius\Component\Report\DataFetcher\Data;
use Sylius\Component\Report\Model\ReportInterface;
use Sylius\Component\Report\Renderer\RendererInterface;
use Symfony\Component\Templating\EngineInterface;

class CSVRenderer implements RendererInterface
{
    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * Path to directory to store csv files.
     *
     * @var string
     */
    protected $path;

    /**
     * @param EngineInterface $templating
     * @param string          $path
     */
    public function __construct(EngineInterface $templating, $path)
    {
        $this->templating = $templating;
        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function render(ReportInterface $report, Data $data)
    {
        if (null !== $data->getData() && 0 !== count($data->getData())) {
            $rendererConfiguration = $report->getRendererConfiguration();

            $writer = new CsvWriter();
            $writer->setStream(fopen($this->path.$rendererConfiguration['filename'], 'w'));

            $rows = $data->getData();
            $writer->writeItem(array_keys($rows[0]));
            foreach ($rows as $row) {
                $writer->writeItem($row);
            }
            $writer->finish();

            return $this->templating->render('SyliusReportBundle:CSV:default.html.twig', [
                'filename' => $rendererConfiguration['filename'],
            ]);
        }

        return $this->templating->render('SyliusReportBundle::noDataTemplate.html.twig', [
            'report' => $report,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return CSVConfigurationType::class;
    }
}
