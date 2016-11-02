<?php

namespace StatsBundle\Controller;

use Doctrine\ORM\EntityManager;
use StatsBundle\Entity\Skill;
use StatsBundle\Form\SkillType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminController extends Controller
{
    public function indexAction($page)
    {
        $nbPerPage = $this->getParameter('nb_item_per_page');

        // On récupère notre objet Paginator
        $users = $this->getDoctrine()
            ->getManager()
            ->getRepository('StatsBundle:User')
            ->getUsers($page, $nbPerPage)
        ;
        $nbPages = ceil(count($users) / $nbPerPage);
        if ($page < 1 || $page > $nbPages) {
            throw $this->createNotFoundException("La page $page n'existe pas.");
        }

        return $this->render('admin/index.html.twig', ['admin' => $this->getUser(), 'nbPages' => $nbPages, 'users' => $users]);
    }

    public function viewSkillAction(Skill $skill)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        if (null === $skill) {
            throw new NotFoundHttpException("La compétence n'existe pas.");
        }

        return $this->render('StatsBundle:Skill:view.html.twig', array(
            'skill' => $skill
        ));
    }

    public function listSkillsAction($page)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $listSkills = $em
            ->getRepository('StatsBundle:Skill')
            ->findAll();
        $nbPerPage = $this->getParameter('nb_item_per_page');

        $nbPages = ceil(count($listSkills) / $nbPerPage);
        if ($nbPages > 0 && ($page < 1 || $page > $nbPages)) {
            throw $this->createNotFoundException("La page $page n'existe pas.");
        }

        return $this->render('StatsBundle:Skill:list.html.twig', array(
            'listSkills' => $listSkills,
            'nbPages'    => $nbPages,
            'page'       => $page
        ));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function addSkillAction(Request $request)
    {
        $skill = new Skill();

        $form = $this->createForm(SkillType::class, $skill);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($skill);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Compétence bien enregistrée.');

            return $this->redirectToRoute('sms_view_skill', array('id' => $skill->getId()));
        }

        return $this->render('StatsBundle:Skill:add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Skill $skill
     * @param Request $request
     *
     * @ParamConverter("skill", class="StatsBundle:Skill")
     *
     * @return Response
     */
    public function editSkillAction(Skill $skill, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if (null === $skill) {
            throw new NotFoundHttpException("Cette compétence n'existe pas.");
        }

        $form = $this->get('form.factory')->create(SkillType::class, $skill);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Compétence bien modifiée.');

            return $this->redirectToRoute('sms_view_skill', array('id' => $skill->getId()));
        }

        return $this->render('StatsBundle:Skill:edit.html.twig', array(
            'skill' => $skill,
            'form'   => $form->createView(),
        ));
    }


    public function deleteSkillAction(Request $request, Skill $skill)
    {
        $em = $this->getDoctrine()->getManager();

        if (null === $skill) {
            throw new NotFoundHttpException("La compétence n'existe pas.");
        }

        $form = $this->get('form.factory')->create();

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em->remove($skill);
            $em->flush();

            $request->getSession()->getFlashBag()->add('info', "La compétence a bien été supprimée.");

            return $this->redirectToRoute('sms_home');
        }

        return $this->render('StatsBundle:Skill:delete.html.twig', array(
            'skill' => $skill,
            'form'  => $form->createView(),
        ));
    }

    public function menuAction($limit)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $listAdverts = $em->getRepository('StatsBundle:User')->getUsers(1, $limit);

        return $this->render('StatsBundle:Skill:menu.html.twig', array(
            'listUsers' => $listAdverts
        ));
    }
}
