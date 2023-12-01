<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Validator\Constraints\Regex;

class LoginController extends AbstractController
{
    #[Route('/login/begin', name: 'app_login_begin')]
    public function begin(
        Request $request,
        ParticipantRepository $participantRepo,
    ): Response {
        $partips = $participantRepo->findAll();
        $form = $this->createFormBuilder([])
            ->add('nickname', EntityType::class, [
                'class' => Participant::class,
                'choice_label' => 'nickname',
                'label' => false,
                'required' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Mai departe'
            ])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $nick = $data['nickname']->getNickname();
            $particip = current(array_filter($partips, function ($p) use ($nick) {
                return $p->getNickname() === $nick;
            }));
            if ($particip->getPasscode() !== null) {
                return $this->redirectToRoute('app_login_login', ['nickname' => $nick]);
            } else {
                return $this->redirectToRoute('app_login_register', ['nickname' => $nick]);
            }
        }

        return $this->render('login/begin.html.twig', [
            'form' => $form->createView(),
            'partips' => $partips
        ]);
    }

    #[Route('/login/login', name: 'app_login_login')]
    public function login(
        ParticipantRepository $participantRepo,
        FormFactoryInterface $formFactory,
        AuthenticationUtils $authUtils,
        Request $request,
    ) {
        if (!isset($_GET['nickname']))
            return $this->redirectToRoute('app_login_begin');
        $nickname = $_GET['nickname'];

        $particip = $participantRepo->findOneBy(['nickname' => $nickname]);
        $formParticip = new Participant();
        $formParticip->setNickname($particip->getNickname());
        $form = $formFactory->createNamedBuilder("", FormType::class, $formParticip)
            ->setAction($this->generateUrl('app_login_login', ['nickname' => $nickname]))
            ->add('_username', HiddenType::class, [
                'property_path' => 'nickname',
            ])
            ->add('_target_path', HiddenType::class, [
                'mapped' => false,
                'data' => '/'
            ])
            ->add('_password', PasswordType::class, [
                'label' => false,
                'required' => true,
                'mapped' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Mai departe'
            ])
            ->getForm();
        $authError = $authUtils->getLastAuthenticationError();
        $error = null;
        if ($authError instanceof BadCredentialsException)
            $error = 'PIN incorect';
        else if ($authError !== null)
            $error = $authError->getMessage();
        return $this->render('login/login.html.twig', [
            'particip' => $particip,
            'form' => $form->createView(),
            'error' =>  $error,
            'query' => $_GET,
            'ratelimit' => $request->getSession()->get('auth.ratelimit'),
        ]);
    }

    #[Route('/login/register', name: 'app_login_register')]
    public function register(
        Security $security,
        UserPasswordHasherInterface $hasher,
        ManagerRegistry $doctrine,
        Request $request,
        ParticipantRepository $participantRepo,
    ) {
        $particip = $participantRepo->findOneBy(['nickname' => $_GET['nickname']]);
        $form = $this->createFormBuilder(new Participant())
            ->add('passcode', PasswordType::class, [
                'label' => false,
                'required' => true,
                'constraints' => [
                    new Regex("/^[0-9]{4}$/", "PIN-ul tău trebuie să fie de exact 4 cifre!")
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Mai departe'
            ])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            // set the passcode
            $hashed = $hasher->hashPassword($particip, $data->getPasscode());
            $particip->setPasscode($hashed);
            $doctrine->getManager()->flush();
            $security->login($particip);
            return $this->redirectToRoute('app_home');
        }
        return $this->render('login/register.html.twig', [
            'particip' => $particip,
            'form' => $form->createView(),
            'query' => $_GET,
        ]);
    }
}
