<?php

namespace SKCMS\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ContactInfoController extends Controller
{
    public function mainAction($_locale,\Symfony\Component\HttpFoundation\Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $contactInfoRepo = $em->getRepository('SKCMSCoreBundle:ContactInfos');

        
        $contactInfo = $contactInfoRepo->findOneOrNullBy([],null,null,$_locale);
        
        
        
        if (null === $contactInfo)
        {
            $contactInfo = new \SKCMS\CoreBundle\Entity\ContactInfos();
        }
        else
        {
//            $contactInfo->setTranslatableLocale($_locale);
//            $em->refresh($contactInfo);
        }
        $contactInfo->setTranslatableLocale($_locale);
        
        
        $form = $this->createForm(new \SKCMS\CoreBundle\Form\ContactInfosType(),$contactInfo);
        
        if ($request->getMethod() == 'POST')
        {
//            $form->handleRequest($request);
            $form->bind($request);
            if ($form->isValid())
            {
                
                $em->persist($contactInfo);
//                dump($request);
//                dump($contactInfo->getTranslatableLocale());
//                die();
                $em->flush();
                
                $this->get('session')->getFlashBag()->add(
                'success',
                'Informations Edited :)'
                );
              $url = $this->generateUrl('sk_admin_contactinfos');
              return $this->redirect($url);
            }
            else
            {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Oops, Informations Edited not edited, sorry, try again :/'
                );
            }
        }
        $siteInfo = $this->container->getParameter('skcms_admin.siteinfo');
        return $this->render('SKCMSAdminBundle:Page:edit-contact-info.html.twig',['form'=>$form->createView(),'entity'=>$contactInfo,'siteinfo'=>$siteInfo]);
    }
    
}
