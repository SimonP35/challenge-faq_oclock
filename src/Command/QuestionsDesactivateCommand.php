<?php

namespace App\Command;

use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class QuestionsDesactivateCommand extends Command
{
    protected static $defaultName = 'app:questions:desactivate';

    // Services nécessaires
    private $qr;
    private $ar;
    private $em; 

    public function __construct(QuestionRepository $qr, AnswerRepository $ar, EntityManagerInterface $em)
    {
        $this->qr = $qr;
        $this->ar = $ar;
        $this->em = $em;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Desactivate all questions that have been updated more than 7 days ago')
            ->addArgument('id', InputArgument::OPTIONAL, 'Question id to fetch')
            ->addOption('dump', null, InputOption::VALUE_NONE, 'Dump checked question id')
            ->addOption('activate', null, InputOption::VALUE_NONE, 'Activate checked question')
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Activate checked question', 7)
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
                $io->error('You need to add an argument to use the option --activate');
                return 0;
            }

            $now = new DateTime();
            $updatedAt = $question->getUpdatedAt();

            if ($updatedAt != null) {

                $interval = $now->diff($updatedAt);

                if ($interval->days >= $input->getOption('limit') && $question->getActive() === true) {
                    $question->setActive(false);
                    $io->success('*** Question n°' . $question->getId() . ' has been desactivated ***');
                }

            } else {

                $io->note('No update date for ' . $question->getId());
            }
        }

        $this->em->flush();

        $io->success('You have checked and desactivated all the olds questions :) !');

        return 0;
    }
}
