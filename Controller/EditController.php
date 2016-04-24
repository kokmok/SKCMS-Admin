<?php

namespace SKCMS\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EditController extends Controller
{
    public function indexAction($entity,$id,$_locale)
    {
        $locale = $_locale;
        $request = $this->get('request');

        $user = $this->getUser();
        $entitiesParams = $this->container->getParameter('skcms_admin.entities');

        $entityParams = $entitiesParams[$entity];

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository($entityParams['class']);

        if ($id!==null)
        {
            $entity = $repo->findForAdmin($id,$locale);

        }
        else
        {
            $entity = new $entityParams['class'];
            $entity->setUserCreate($user);

        }

        $entity->setUserUpdate($user);
        $entity->setUpdateDate(new \DateTime());
        $entity->setTranslatableLocale($locale);

//        die($entity->getPicture()->getPicture().'te');

//        dump($entity);
//        die();


        if (isset($entityParams['formParams']) && count($entityParams['formParams']))
        {
            if (isset($entityParams['formParams']['user']) && $entityParams['formParams']['user'] == 'current')
            {
                $form = $this->createForm(new $entityParams['form']($user),$entity);
            }
            if (isset($entityParams['formParams']['method']))
            {
                $class = new $entityParams['formParams']['class'];
                $formParams = call_user_func( array( $class, $entityParams['formParams']['method'] ) );
//                die('t'.$formParams);
                $form = $this->createForm(new $entityParams['form']($formParams),$entity);
            }
        }
        else
        {
            $form = $this->createForm(new $entityParams['form'],$entity);
        }

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN'))
        {
            $superAdminElements = ['lists','menus','minRoleAccess','redirectRoute','forward'];
            foreach ($superAdminElements as $superAdminElement)
            {
                if ($form->has($superAdminElement))
                {
                    $form->remove($superAdminElement);
                }
            }

        }

        if ($request->getMethod() == 'POST')
        {

            $form->bind($request);

            if ($form->isValid())
            {

                if (is_subclass_of($entity,'\SKCMS\CoreBundle\Entity\SkBaseEntity'))
                {
                    $newPosition = $repo->findLastSortableIndex('position')+1;
                    $entity->setPosition($newPosition);
                }

                $em->persist($entity);
                $em->flush();



                $this->get('session')->getFlashBag()->add(
                    'success',
                    $entityParams['beautyName'].' Edited :)'
                );

                if ($request->request->get('stay_here') !== null)
                {
                    $url = $this->generateUrl('sk_admin_edit',['entity'=>$entityParams['name'],'_locale'=>$locale,'id'=>$entity->getId()]);
                }
                elseif ($request->request->get('add_new') !== null)
                {
                    $url = $this->generateUrl('sk_admin_edit',['entity'=>$entityParams['name'],'_locale'=>$this->container->getParameter('locale')]);
                }
                else
                {
                    $url = $this->generateUrl('sk_admin_list',['entity'=>$entityParams['name']]);
                }

                return $this->redirect($url);
            }
            else
            {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Oops, '.$entityParams['beautyName'].' not edited, sorry, try again :/'
                );
            }

        }

        $siteInfo = $this->container->getParameter('skcms_admin.siteinfo');


        return $this->render('SKCMSAdminBundle:Page:edit.html.twig',['entityParams'=>$entityParams,'entity'=>$entity,'form'=>$form->createView(),'siteinfo'=>$siteInfo]);
    }

    public function deleteAction($entity,$id)
    {
        $user = $this->getUser();
        $entitiesParams = $this->container->getParameter('skcms_admin.entities');

        $entityParams = $entitiesParams[$entity];

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository($entityParams['class']);
        $entity = $repo->find($id);


        $em->remove($entity);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'success',
            $entityParams['beautyName'].' deleted'
        );

        $url = $this->generateUrl('sk_admin_list',['entity'=>$entityParams['name']]);
        return $this->redirect($url);

    }

    public function pullDownAction($entity,$id,$_locale)
    {
        $locale = $_locale;

        $entitiesParams = $this->container->getParameter('skcms_admin.entities');
        $entityParams = $entitiesParams[$entity];
        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository($entityParams['class']);

        $entity = $repo->find($id,$locale);

        $position = call_user_method('get'.ucfirst($entityParams['sort']), $entity);

        $nextEntity = $repo->findNextEntity($entityParams['sort'],$position);

        $nextPotition = call_user_method('get'.ucfirst($entityParams['sort']), $nextEntity);
        call_user_method('set'.ucfirst($entityParams['sort']), $entity,$nextPotition);
        call_user_method('set'.ucfirst($entityParams['sort']), $nextEntity,$position);



        $em->persist($entity);
        $em->persist($nextEntity);

        $em->flush();

        return new \Symfony\Component\HttpFoundation\JsonResponse(['status'=>1]);
    }

    public function pushUpAction($entity,$id,$_locale)
    {
        $locale = $_locale;

        $entitiesParams = $this->container->getParameter('skcms_admin.entities');

        $entityParams = $entitiesParams[$entity];

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository($entityParams['class']);

        $entity = $repo->find($id,$locale);

        $position = call_user_func([$entity,'get'.ucfirst($entityParams['sort'])]);

        $prevEntity = $repo->findPrevEntity($entityParams['sort'],$position);


        $prevPotition = call_user_func([$prevEntity,'get'.ucfirst($entityParams['sort'])]);


//
        call_user_func([$entity,'set'.ucfirst($entityParams['sort'])], $prevPotition);
        call_user_func([$prevEntity,'set'.ucfirst($entityParams['sort'])], $position);
//        call_user_method



        $em->persist($entity);
        $em->persist($prevEntity);

        $em->flush();

        return new \Symfony\Component\HttpFoundation\JsonResponse(['status'=>1]);

    }
}
