<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class BaseFixture extends Fixture
{
    private SymfonyStyle $io;

    private ProgressBar $progressBar;

    public function load(ObjectManager $manager): void
    {
        $this->prepareLoader($manager);

        $manager->flush();
        $manager->clear();
    }

    abstract protected function prepareLoader(ObjectManager $manager): void;

    protected function createMany(int $amount, callable $factory, ObjectManager $manager): void
    {
        $this->progressBar = $this->createProgressBar($amount);

        for ($i = 0; $i < $amount; $i++) {
            $entity = $factory(Factory::create(), $this);

            $manager->persist($entity);

            $this->progressBar->advance();
        }

        $this->progressBar->setMessage('');
        $this->progressBar->finish();

        $this->io->newLine();
    }

    private function createProgressBar(int $amount): ProgressBar
    {
        $this->io = new SymfonyStyle(new StringInput(''), new ConsoleOutput(OutputInterface::VERBOSITY_DEBUG));

        return $this->io->createProgressBar($amount);
    }
}
