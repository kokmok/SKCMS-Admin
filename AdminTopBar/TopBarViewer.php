<?php
namespace SKCMS\AdminBundle\AdminTopBar;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of TopBarViewer
 *
 * @author Jona
 */
class TopBarViewer
{
    private $twig;

    private $container;


    public function __construct($container)
    {
        $this->container = $container;
        $this->twig= $container->get('twig');

    }


    public function displayAdminTopBar(Response $response,$editPath)
    {
        $adminTopBar = $this->getAdminTopBar($editPath);
        $htmlContent = $response->getContent();

//        die($adminTopBar);
//        die();

//        if (!preg_match('#col-md#',$htmlContent)) //Bootsrap Fix
//        {
        $bootstrap = $this->getBootsrap();
        $htmlContent = preg_replace('#(<head[ a-zA-Z0-9\"\'=_-]*>)#','$1'.$bootstrap,$htmlContent);

//            $bootsrapJS = '<script type="text/javascript" src="'.$this->container->get('templating.helper.assets')->getUrl('bundles/skcmsadmin/js/bootstrap.min.js').'"></script>';
        $bootsrapJS = '<script type="text/javascript" src="'.$this->container->get('templating.helper.assets')->getUrl('bundles/skcmsadmin/js/topBar.js').'"></script>';
        $htmlContent = preg_replace('#(<\/body>)#',$bootsrapJS.'$1',$htmlContent);
//        }



        $htmlContent = preg_replace('#(<body[ a-zA-Z0-9\"\'=_-]*>)#','$1'.$adminTopBar,$htmlContent);


        $response->setContent($htmlContent);


        return $response;
    }

    private function getBootsrap()
    {
        $bootrap ='';
//        $bootrap =  '<link rel="stylesheet" href="'.$this->container->get('templating.helper.assets')->getUrl('bundles/skcmsadmin/css/topBarGrid.css').'" />';
//        $bootrap .= '<link rel="stylesheet" href="'.$this->container->get('templating.helper.assets')->getUrl('bundles/skcmsadmin/css/bootstrap-theme.min.css').'" />';
        $bootrap .= '<link rel="stylesheet" href="'.$this->container->get('templating.helper.assets')->getUrl('bundles/skcmsadmin/css/font-awesome.min.css').'" />';
        $bootrap .= '<link rel="stylesheet" href="'.$this->container->get('templating.helper.assets')->getUrl('bundles/skcmsadmin/css/topBar.css').'" />';
//        $bootrap .= '<link rel="stylesheet" href="'.$this->container->get('templating.helper.assets')->getUrl('bundles/skcmsadmin/css/zskcms.css').'" />';

        return $bootrap;
    }

    private function getAdminTopBar($editPath)
    {
        $siteInfo = $this->container->getParameter('skcms_admin.siteInfo');
        $entities = $this->container->getParameter('skcms_admin.entities');
        $menuGroups = $this->container->getParameter('skcms_admin.menuGroups');
        $modules = $this->container->getParameter('skcms_admin.modules');


        if ($modules['contact']['enabled'])
        {
            $contactParams = $modules['contact'];
            $em = $this->container->get('doctrine')->getManager();
            $repo = $em->getRepository($contactParams['messageEntity']['class']);

            $contactMessages = $repo->findBy(['status'=>  \SKCMS\ContactBundle\Entity\ContactMessage::STATUS_NEW]);
            $newContactMessageNumber = count($contactMessages);
        }
        else
        {
            $newContactMessageNumber = 0;
        }
//        $this->redirect($url);
        $websitePath = $this->container->get('router')->generate($siteInfo['homeRoute']);

        return $this->twig->render('SKCMSAdminBundle:Element/Nav:top-bar.html.twig',['websitePath'=>$websitePath,'entititesType'=>$entities,'menuGroups'=>$menuGroups,'modules'=>$modules,'newContactMessageNumber'=>$newContactMessageNumber,'editPath'=>$editPath]);
    }



}
