<?php namespace Vdbf\SiteMapper\Console;

use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Vdbf\SiteMapper\Clients\FileGetContentsCrawlClient;
use Vdbf\SiteMapper\Clients\GuzzleCrawlClient;
use Vdbf\SiteMapper\Mapper;

class GenerateMappingCommand extends Command
{

    /**
     * Configure the command with meta-info, options and arguments
     */
    protected function configure()
    {
        $this
            ->setName('sitemap:generate')
            ->setDescription('Generates a site map for the given starting url')
            ->addArgument(
                'url',
                InputArgument::REQUIRED,
                'Root url to start crawling and generating the site map'
            )
            ->addArgument(
                'path',
                InputArgument::OPTIONAL,
                'Sitemap export path',
                null
            )
            ->addOption(
                'pool-size',
                'ps',
                InputOption::VALUE_OPTIONAL,
                'Pool size of request batch',
                50
            )
            ->addOption(
                'print',
                'p',
                InputOption::VALUE_NONE,
                'After completion print the resulting site map to standard output'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pool_size = $input->getOption('pool-size');

        $mapper = Mapper::url($url = $input->getArgument('url'));

        $mapper->setClient(new GuzzleCrawlClient(new Client(), compact('pool_size')));
        $mapper->setLogger(new ConsoleLogger($output));

        $xml = $mapper->crawl()->toXml($input->getArgument('path'));

        if ($input->getOption('print')) {
            $output->writeln($xml);
        }
    }

}