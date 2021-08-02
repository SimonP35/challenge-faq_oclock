<?php

namespace App\Command;

use App\Repository\QuestionRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class QuestionsDeactivateCommand extends Command
{
    protected static $defaultName = 'app:questions:deactivate';

    // Services nécessaires
    private $qr;
    private $em;

    public function __construct(QuestionRepository $qr, EntityManagerInterface $em)
    {
        $this->qr = $qr;
        $this->em = $em;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Deactivate outdated questions')
            ->addArgument('id', InputArgument::OPTIONAL, 'Question id to fetch')
            ->addOption('dump', null, InputOption::VALUE_NONE, 'Dump checked question id')
            ->addOption('activate', null, InputOption::VALUE_NONE, 'Activate checked question')
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Set the number of days for deactivation', 7)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $id = $input->getArgument('id');

        if ($id) {
            $io->note(sprintf('Question to fetch and check: %d', $id));
            $question = $this->qr->findOneBy([ 'id' => $id ]);
            $questions = [$question];
        } else {
            $io->note(sprintf('Fetching all question'));
            $questions = $this->qr->findAll();
        }

        foreach ($questions as $question) {
            if ($input->getOption('dump')) {
                $io->note('Fetching ' . $question->getId());
            }

            if ($input->getOption('activate') && $question->getActive() === false && $id) {
                $question->setActive(true);
                $io->success('*** Question n°' . $question->getId() . ' has been activated ***');
                $this->em->flush();
                return 0;
            } elseif ($input->getOption('activate') && $question->getActive() === true && $id) {
                $io->success('*** Question n°' . $question->getId() . ' is already activated ***');
                return 0;
            } elseif ($input->getOption('activate') && !$id) {
                $io->error('You need to add an argument (id) to use the option --activate');
                return 0;
            }

            $now = new DateTime();
            $updatedAt = $question->getUpdatedAt();

            if ($updatedAt === null) {
                $updatedAt = $question->getCreatedAt();
            }

            $interval = $now->diff($updatedAt);

            if ($interval->days >= $input->getOption('limit') && $question->getActive() === true) {
                $question->setActive(false);
                $io->success('*** Question n°' . $question->getId() . ' has been deactivated ***');
            } elseif ($interval->days < $input->getOption('limit') && $question->getActive() === true) {
                $io->note('*** Question n°' . $question->getId() . ' don\'t need to be deactivated ***');
            } elseif ($question->getActive() === false) {
                $io->note('*** Question n°' . $question->getId() . ' is already deactivated ***');
            }
        }

        $this->em->flush();

        $io->success('You have checked and deactivated all outdated questions :) !');

        return 0;
    }

    //? Correction cours :
    // protected function execute(InputInterface $input, OutputInterface $output): int
    // {
    //     $io = new SymfonyStyle($input, $output);

    //     // Option 1 : On récupère toutes les Q. et on traite en PHP
    //     // => bien mais si SQL peut le faire pour nous, autant le faire bosser

    //     // Option 2 : On récupère les questions concernées directement en SQL
    //     // => Go for it ! (on préférera faire travailler MySQL que PHP)

    //     // On récupère les questions (via QuestionRepository)
    //     $nbAffectedRows = $this->questionRepository->updateAllOutdated();

    //     $io->success('You have checked and deactivated ' . $nbAffectedRows . ' questions !');

    //     return 0;
    // }
}