<?php

namespace Fly\PlatformBundle\Command;

use Fly\PlatformBundle\Entity\Airport;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;

class AirportsLoadCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('airports:load')
            ->setDescription('Load Airports Data');
//            ->addArgument(
//                'name',
//                InputArgument::OPTIONAL,
//                'Who do you want to greet?'
//            )
//            ->addOption(
//                'yell',
//                null,
//                InputOption::VALUE_NONE,
//                'If set, the task will yell in uppercase letters'
//            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
//        $csvPath = __DIR__.'/../../../../app/Resources/csv/airport-codes.csv';
        $csvPath = __DIR__.'/../../../../app/Resources/csv/airport-codes-test.csv';
        $row = 1;
        if (($handle = fopen($csvPath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
              $airport = new Airport();
              $airport->setCityName($data[0]);
              $airport->setCityCode($data[1]);
              $airport->setAirportCode($data[2]);
              $airport->setAirportName($data[3]);
              $airport->setCountryName($data[4]);
              $airport->setCountryAbbrev($data[5]);
              $airport->setWorldAreaCode($data[6]);
                $em->persist($airport);
                $em->flush();
                $output->writeln( $row . " - Airport Data loaded " . $data[1].'-'.$data[2] );
                $row++;
            }
            fclose($handle);
        }
        $text = 'Data loads done';

        $output->writeln($text);
    }
}