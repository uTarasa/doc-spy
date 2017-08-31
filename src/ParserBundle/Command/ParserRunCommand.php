<?php

namespace ParserBundle\Command;

use ParserBundle\Entity\Source;
use ParserBundle\Repository\SourceRepository;
use ParserBundle\Service\ParserService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ParserRunCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('parser:run')
            ->setDescription('Parse document');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var SourceRepository $sourceRepository */
        $sourceRepository = $this->getContainer()->get('doctrine')->getRepository(Source::class);
        $source = $sourceRepository->findNextSource();

        /** @var ParserService $parser */
        $parser = $this->getContainer()->get('parser');
        $parser->read($source);

        $output->writeln('Parsed: ' . $source->getName());
        $output->writeln('Received items: ' . $parser->getAllCount() .
            ($parser->getAddedCount() ? '. <info>new items: ' . $parser->getAddedCount() . '</info>' : '')
        );
    }
}
