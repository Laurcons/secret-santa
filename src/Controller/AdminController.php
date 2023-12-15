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
        $assignedList = [];
        $total = count($particips);
        while (count($assignedList) !== count($particips)) {
            $giftee = null;
            while ($giftee === null) {
                $idx = rand(0, $total - 1);
                $candidate = $particips[$idx];
                if (
                    $currentP->getId() !== $candidate->getId() &&
                    // allow gifting idx 0 ONLY when everyone else is gifted
                    (count($assignedList) === $total - 1 || $candidate->getId() !== $particips[0]->getId()) &&
                    array_search($candidate->getId(), $assignedList) === false
                ) {
                    $giftee = $candidate;
                }
            }
            // create the assignation from currentP to assigned
            $asn = new Assignation();
            $asn->setGifter($currentP);
            $asn->setGiftee($giftee);
            $manager->persist($asn);
            // prepare next loop
            $assignedList[] = $giftee->getId();
            $currentP = $giftee;
        }
        $manager->flush();

        return $this->redirectToRoute("app_santa");
    }

    #[Route('/admin/presentation', 'app_admin_presentation')]
    public function presentation(
        AssignationRepository $asnRepo
    ) {
        $asns = $asnRepo->findAll();
        return $this->render('admin/presentation.html.twig', [
            'frontendData' => [
                'assignations' => array_map(function ($item) {
                    return [
                        'gifter' => [
                            'nickname' => $item->getGifter()->getNickname(),
                            'emoji' => $item->getGifter()->getEmoji(),
                        ],
                        'giftee' => [
                            'nickname' => $item->getGiftee()->getNickname(),
                            'emoji' => $item->getGiftee()->getEmoji(),
                        ]
                    ];
                }, $asns),
            ]
        ]);
    }
}
