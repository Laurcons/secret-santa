<?php

namespace App\Controller;

use App\Entity\Assignation;
use App\Repository\AssignationRepository;
use App\Repository\ParticipantRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin/generate_assignations', methods: ['POST'], name: 'app_admin_generate_assignations')]
    public function index(
        ParticipantRepository $participRepo,
        AssignationRepository $asnRepo,
        ManagerRegistry $doctrine,
    ): Response {
        $manager = $doctrine->getManager();

        // get all participants
        $particips = $participRepo->createQueryBuilder('p')
            ->leftJoin('p.participantInfo', 'pi')
            ->where('pi IS NOT NULL')
            ->getQuery()
            ->getResult();

        $currentP = $particips[0];
        $assignedIds = [];
        $total = count($particips);
        $remaining = $total;
        while ($remaining !== 0) {
            $assigned = null;
            while ($assigned === null) {
                $idx = rand(0, $total - 1);
                $asn = $particips[$idx];
                if (array_search($asn->getId(), $assignedIds) === false) {
                    $assigned = $asn;
                }
            }
            // create the assignation from currentP to assigned
            $asn = new Assignation();
            $asn->setGifter($currentP);
            $asn->setGiftee($assigned);
            $manager->persist($asn);
            // prepare next loop
            $assignedIds[] = $assigned->getId();
            $remaining--;
            $currentP = $assigned;
        }
        $manager->flush();

        return $this->redirectToRoute("app_santa");
    }
}
