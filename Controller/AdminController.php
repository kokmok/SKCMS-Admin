<?php

namespace SKCMS\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    public function indexAction()
    {

        

        $today = new \DateTime();
        $sessionRepo = $this->getDoctrine()->getManager()->getRepository('SKCMSTrackingBundle:Session');
        $sessionsByMonth = [];
        $newDate = $today;
        $i =0;
        while ($i<6)
        {

            $nombreSession = $sessionRepo->countByMonth($newDate);
            $sessionsByMonth[] = ['month'=>$newDate->format('F'),'count'=>$nombreSession];
            $newDate->modify('- 1month');
            $i++;
        }

        $viewRepo = $this->getDoctrine()->getManager()->getRepository('SKCMSTrackingBundle:View');
        $today = new \DateTime();
        $dateWalker = new \DateTime();
        $viewsByDay = $viewRepo->findGrouppedByDay($dateWalker->modify('-1 month'));

        $lastMonthViews = [];
        $viewsIndex = 0;
        $totalCount = 0;

        while ($dateWalker->format('Ymd')<=$today->format('Ymd'))
        {
            $count = 0;
            if (isset($viewsByDay[$viewsIndex]) && $viewsByDay[$viewsIndex]['vDate']->format('Y-m-d') == $dateWalker->format('Y-m-d'))
            {
                $count = $viewsByDay[$viewsIndex]['vCount'];
                $totalCount += $count;
                $viewsIndex++;
            }
            $label = $dateWalker->format('d/m');

            $lastMonthViews[] = ['count'=>$count,'label'=>$label];
            $dateWalker->modify('+1 day');
        }

        $modulesParams = $this->container->getParameter('skcms_admin.modules');
        $contactParams = $modulesParams['contact'];
        if ($contactParams['enabled'] == true)
        {
            $contactRepo = $this->getDoctrine()->getManager()->getRepository($contactParams['messageEntity']['bundle'].'Bundle:'.$contactParams['messageEntity']['name']);
            $contactMessages = $contactRepo->findBy([],['date'=>'DESC'],10);
        }
        else
        {
            $contactMessages = [];
        }


        return $this->render('SKCMSAdminBundle:Page:index.html.twig',['sessionByMonth'=>  array_reverse($sessionsByMonth),'lastMonthViews'=>$lastMonthViews,'totalLastMonthViews'=>$totalCount,'contactMessages'=>$contactMessages,'entityParams'=>$contactParams]);
    }

    public function leftNavAction()
    {

        $entities = $this->container->getParameter('skcms_admin.entities');
        $menuGroups = $this->container->getParameter('skcms_admin.menuGroups');
        $modules = $this->container->getParameter('skcms_admin.modules');

        return $this->render('SKCMSAdminBundle:Element/Nav:left-bar.html.twig',['entititesType'=>$entities,'menuGroups'=>$menuGroups,'modules'=>$modules]);
    }
    public function topNavAction()
    {
        /*
         * A voir si il n'y a pas un bug, c'est bizarre cet écrasement de $entities
         */
        $siteInfo = $this->container->getParameter('skcms_admin.siteInfo');
        $entities = $this->container->getParameter('skcms_admin.entities');
        $menuGroups = $this->container->getParameter('skcms_admin.menuGroups');
        $modules = $this->container->getParameter('skcms_admin.modules');

        if ($modules['contact']['enabled'])
        {
            $contactParams = $modules['contact'];
            $em = $this->getDoctrine()->getManager();
            $repo = $em->getRepository($contactParams['messageEntity']['bundle'].'Bundle:'.$contactParams['messageEntity']['name']);

            $entities = $repo->findBy(['status'=>  \SKCMS\ContactBundle\Entity\ContactMessage::STATUS_NEW]);
            $newContactMessageNumber = count($entities);
        }
        else
        {
            $newContactMessageNumber = count($entities);
        }
//        $this->redirect($url);
        $websitePath = $this->generateUrl($siteInfo['homeRoute']);
        return $this->render('SKCMSAdminBundle:Element/Nav:top-bar.html.twig',['websitePath'=>$websitePath,'entititesType'=>$entities,'menuGroups'=>$menuGroups,'modules'=>$modules,'newContactMessageNumber'=>$newContactMessageNumber]);
    }
}
