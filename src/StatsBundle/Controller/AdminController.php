<?php

namespace StatsBundle\Controller;

use Doctrine\ORM\EntityManager;
use StatsBundle\Entity\Skill;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminController extends Controller
{
    public function indexAction($page)
    {
        $nbPerPage = $this->getParameter('nb_user_per_page');

        // On récupère notre objet Paginator
        $listAdverts = $this->getDoctrine()
            ->getManager()
            ->getRepository('StatsBundle:User')
            ->getUsers($page, $nbPerPage)
        ;
        $nbPages = ceil(count($listAdverts) / $nbPerPage);
        if ($page < 1 || $page > $nbPages) {
            throw $this->createNotFoundException("La page $page n'existe pas.");
        }

        return $this->render('admin/index.html.twig', ['admin' => $this->getUser()]);
    }

    public function viewSkillAction(Skill $skill)
    {
        $em = $this->getDoctrine()->getManager();

        if (null === $skill) {
            throw new NotFoundHttpException("La compétence n'existe pas.");
        }

        $listAdvertSkills = $em
            ->getRepository('OCPlatformBundle:AdvertSkill')
            ->findBy(array('skill' => $skill));

        return $this->render('StatsBundle:Skill:view.html.twig', array(
            'skill'          => $skill,
            'listUserSkills' => $listAdvertSkills
        ));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function addSkillAction(Request $request)
    {
        $skill = new Skill();

        $form = $this->createForm(Skill::class, $skill);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($skill);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Compétence bien enregistrée.');

            return $this->redirectToRoute('sms_skill_view', array('id' => $skill->getId()));
        }

        return $this->render('StatsBundle:Skill:add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function editSkillAction(Skill $skill, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if (null === $skill) {
            throw new NotFoundHttpException("Cette compétence n'existe pas.");
        }

        $form = $this->get('form.factory')->create(Skill::class, $skill);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Compétence bien modifiée.');

            return $this->redirectToRoute('sms_skill_view', array('id' => $skill->getId()));
        }

        return $this->render('StatsBundle:Skill:edit.html.twig', array(
            'skill' => $skill,
            'form'   => $form->createView(),
        ));
    }

    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository('StatsBundle:Skill')->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("La compétence d'id $id n'existe pas.");
        }

        $form = $this->get('form.factory')->create();

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em->remove($advert);
            $em->flush();

            $request->getSession()->getFlashBag()->add('info', "La compétence a bien été supprimée.");

            return $this->redirectToRoute('sms_home');
        }

        return $this->render('OCPlatformBundle:Skill:delete.html.twig', array(
            'advert' => $advert,
            'form'   => $form->createView(),
        ));
    }

    public function menuAction($limit)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $listAdverts = $em->getRepository('StatsBundle:User')->getUsers(1, $limit);

        return $this->render('StatsBundle:User:menu.html.twig', array(
            'listUsers' => $listAdverts
        ));
    }
}
