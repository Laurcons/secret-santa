<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\ParticipantInfo;
use App\Repository\AssignationRepository;
use App\Repository\ParticipantRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class SantaController extends AbstractController
{
    #[Route('/santa', name: 'app_santa')]
    public function index(
        #[CurrentUser] Participant $particip,
        AssignationRepository $asnRepo,
    ): Response {
        if ($asnRepo->count([]) === 0) {
            if ($particip->getParticipantInfo() !== null) {
                return $this->redirectToRoute("app_santa_await");
            } else {
                return $this->redirectToRoute("app_santa_enroll");
            }
        }
        return $this->redirectToRoute("app_santa_assignation");
    }

    #[Route('/santa/enroll', name: 'app_santa_enroll')]
    public function enroll(
        Request $request,
        #[CurrentUser] Participant $particip,
        ManagerRegistry $doctrine,
        AssignationRepository $asnRepo,
    ): Response {
        if ($asnRepo->count([]) !== 0 || $particip->getParticipantInfo() !== null)
            return $this->redirectToRoute('app_santa');
        $form = $this->createFormBuilder()
            ->add('enroll', SubmitType::class, [
                'label' => "Vreau să particip la Secret Santa!"
            ])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $info = new ParticipantInfo();
            $info->setParticipant($particip);
            $info->setWishlist(null);
            $doctrine->getManager()->persist($info);
            $doctrine->getManager()->flush();
            return $this->redirectToRoute('app_santa');
        }
        return $this->render('santa/enroll.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/santa/await', name: 'app_santa_await')]
    public function await(
        #[CurrentUser] Participant $particip,
        AssignationRepository $asnRepo,
    ): Response {
        if ($asnRepo->count([]) !== 0 || $particip->getParticipantInfo() === null)
            return $this->redirectToRoute('app_santa');

        return $this->render('santa/await.html.twig', [
            'particip' => $particip
        ]);
    }

    #[Route('/santa/assignation', name: 'app_santa_assignation')]
    public function assignation(
        #[CurrentUser] Participant $particip,
    ): Response {

        $form = $this->createFormBuilder($particip->getGifterAssignation())
            ->add('presentStatus', ChoiceType::class, [
                'label' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Actualizează"
            ])
            ->getForm();

        $priceCeiling = $this->getParameter('santa.price_ceiling');
        $deadline = $this->getParameter('santa.deadline');
        $anyConfig = false;
        if ($priceCeiling !== null || $deadline !== null)
            $anyConfig = true;

        return $this->render('santa/assignation.html.twig', [
            'particip' => $particip,
            'form' => $form->createView(),
            'santa' => [
                'anyConfig' => $anyConfig,
                'priceCeiling' => $priceCeiling,
                'deadline' => $deadline,
            ]
        ]);
    }
}
