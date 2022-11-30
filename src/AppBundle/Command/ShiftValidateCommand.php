<?php
// src/AppBundle/Command/ShiftValidateCommand.php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShiftValidateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:shift:validate')
            ->setDescription('Validate completed shifts')
            ->setHelp('This command allows you to validate past shifts not dismissed')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $shiftRepository = $em->getRepository('AppBundle:Shift');
        $qb = $shiftRepository
            ->createQueryBuilder('s');
        $qb->where('s.end < :today')
            ->andWhere('s.shifter > 0')
            ->andWhere('s.isDismissed = 0')
            ->andWhere('s.wasCarriedOut = 0')
            ->setParameter('today', date('Y-m-d 00:00:00'))
            ->orderBy('s.end', 'ASC');

        $shifts = $qb->getQuery()->getResult();

        $count = 0;
        foreach ($shifts as $shift) {
            $shift->validateShiftParticipation();
            $em->persist($shift);
            $count++;
        }
        $em->flush();

        $message = $count.' créneau'.(($count>1) ? 'x':'').' validé'.(($count>1) ? 's':'');
        $output->writeln('<fg=cyan;>>>></><fg=green;> '.$message.' </>');
    }
}
