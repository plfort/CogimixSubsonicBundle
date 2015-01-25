<?php
namespace Cogipix\CogimixSubsonicBundle\Controller;


use Cogipix\CogimixCommonBundle\Utils\AjaxResult;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Cogipix\CogimixSubsonicBundle\Form\SubsonicServerInfoFormType;
use Cogipix\CogimixSubsonicBundle\Form\SubsonicServerInfoEditFormType;
use Cogipix\CogimixSubsonicBundle\Entity\SubsonicServerInfo;

/**
 * @Route("/subsonic")
 *
 * @author plfort - Cogipix
 *
 */
class DefaultController extends Controller
{

    /**
     * @Secure(roles="ROLE_USER")
     * @Route("/manageModal",name="_subsonic_manage_modal",options={"expose"=true})
     */
    public function getManageModalAction(Request $request)
    {
        $response = new AjaxResult();
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $subsonicInfos = $em->getRepository('CogimixSubsonicBundle:SubsonicServerInfo')->findByUser($user);
        $response->setSuccess(true);
        $response->addData('modalContent', $this->renderView('CogimixSubsonicBundle:SubsonicServerInfo:modalContent.html.twig', array(
            'subsonicInfos' => $subsonicInfos
        )));
        return $response->createResponse();
    }


    /**
     * @Secure(roles="ROLE_USER")
     * @Route("/create",name="_subsonic_create",options={"expose"=true})
     */
    public function createSubsonicInfoAction(Request $request)
    {
        $response = new AjaxResult();
        $actionUrl = $this->generateUrl('_subsonic_create');
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $subsonicInfo = new SubsonicServerInfo();

        $subsonicInfo->setUser($user);
        $action = 'create';
        $response->addData('formType', $action);

        $form = $this->createForm(new SubsonicServerInfoFormType(), $subsonicInfo);
        if ($request->getMethod() === 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $em->persist($subsonicInfo);
                $em->flush();
                $response->setSuccess(true);
                $response->addData('newItem', $this->renderView('CogimixSubsonicBundle:SubsonicServerInfo:listItem.html.twig', array(
                    'subsonicInfo' => $subsonicInfo
                )));
            } else {
                $response->setSuccess(false);
                $response->addData('formHtml', $this->renderView('CogimixSubsonicBundle:SubsonicServerInfo:formContent.html.twig', array(
                    'action' => $action,
                    'actionUrl' => $actionUrl,
                    'subsonicInfo' => $subsonicInfo,
                    'form' => $form->createView()
                )));
            }
        } else {
            $response->setSuccess(true);
            $response->addData('formHtml', $this->renderView('CogimixSubsonicBundle:SubsonicServerInfo:formContent.html.twig', array(
                'action' => $action,
                'actionUrl' => $actionUrl,
                'subsonicInfo' => $subsonicInfo,
                'form' => $form->createView()
            )));
        }

        return $response->createResponse();
    }

    /**
     * @Secure(roles="ROLE_USER")
     * @Route("/edit/{id}",name="_subsonic_edit",options={"expose"=true})
     */
    public function editSubsonicServerInfoAction(Request $request, $id)
    {
        $response = new AjaxResult();

        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $subsonicInfo = $em->getRepository('CogimixSubsonicBundle:SubsonicServerInfo')->findOneBy(array('id'=>$id,'user'=>$user));
        if ($subsonicInfo !== null) {
            $actionUrl = $this->generateUrl('_subsonic_edit', array(
                'id' => $id
            ));
            $action = 'edit';
            $response->addData('formType', $action);
            $form = $this->createForm(new SubsonicServerInfoEditFormType(), $subsonicInfo);

            if ($request->getMethod() === 'POST') {
                $form->bind($request);
                if ($form->isValid()) {
                    $em->flush();
                    $response->setSuccess(true);
                } else {

                    $response->setSuccess(false);
                    $response->addData('formHtml', $this->renderView('CogimixSubsonicBundle:SubsonicServerInfo:formContent.html.twig', array(
                        'action' => $action,
                        'actionUrl' => $actionUrl,
                        'subsonicInfo' => $subsonicInfo,
                        'form' => $form->createView()
                    )));
                }
            } else {
                $response->setSuccess(true);
                $response->addData('formHtml', $this->renderView('CogimixSubsonicBundle:SubsonicServerInfo:formContent.html.twig', array(
                    'action' => $action,
                    'actionUrl' => $actionUrl,
                    'subsonicInfo' => $subsonicInfo,
                    'form' => $form->createView()
                )));
            }
        }

        return $response->createResponse();
    }


    /**
     *  @Secure(roles="ROLE_USER")
     *  @Route("/test",name="_subsonicserver_test",options={"expose"=true})
     */
    public function testSubsonicServerAction(Request $request){
        $response = new AjaxResult();

        $subsonicInfo = new SubsonicServerInfo();
        $form = $this->createForm(new SubsonicServerInfoFormType(),$subsonicInfo,array('validation_groups'=>array('Test')));
        $params= $request->request->get('custom_provider_create_form');
        if(isset($params['alias'])){
            unset($params['alias']);
            $request->request->set('subsonic_server_create_form', $params);
        }

        if($request->getMethod()==='POST'){
            $form->bind($request);
            if($form->isValid()){

                $plugin= $this->get('cogimix.subsonic_plugin_factory')->createSubsonicPlugin($subsonicInfo);
                if(($responsePlugin = $plugin->testRemote()) !==false){
                    $response->setSuccess(true);
                    $response->addData('message', $this->get('translator')->trans('cogimix.subsonic_server_test_success'));
                }else{
                    $response->setSuccess(false);
                    $response->addData('message', $this->get('translator')->trans("cogimix.subsonic_server_test_fail"));
                }

            }else{

                $response->setSuccess(false);
                $response->addData('message', $this->get('translator')->trans("cogimix.subsonic_server_info.errors_in_form"));
            }
        }
        return $response->createResponse();
    }

    /**
     * @Secure(roles="ROLE_USER")
     * @Route("/remove/{id}",name="_subsonic_remove",options={"expose"=true})
     */
    public function removeSubsonicServerInfoAction(Request $request, $id)
    {
        $response = new AjaxResult();

        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $subsonicInfo = $em->getRepository('CogimixSubsonicBundle:SubsonicServerInfo')->findOneBy(array('id'=>$id,'user'=>$user));
        if ($subsonicInfo !== null) {
            $em->remove($subsonicInfo);
            $em->flush();
            $response->setSuccess(true);
            $response->addData('id', $id);
        }

        return $response->createResponse();
    }

    /**
     * @Secure(roles="ROLE_USER")
     * @Route("/playlist/{serverId}/{playlistId}",name="_subsonic_playlist_songs",options={"expose"=true})
     */
    public function getPlaylistSongsAction($serverId,$playlistId)
    {
        $ajaxResponse = new AjaxResult();
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $subsonicInfo = $em->getRepository('CogimixSubsonicBundle:SubsonicServerInfo')->findOneBy(array('id'=>$serverId,'user'=>$user));
        if($subsonicInfo){
            $subsonicPlugin = $this->get('cogimix.subsonic_plugin_factory')->createSubsonicPlugin($subsonicInfo);
            $tracks = $subsonicPlugin->getPlaylistTracks($playlistId);
            $ajaxResponse->addData('tracks', $tracks);
            $ajaxResponse->setSuccess(true);
        }

        return $ajaxResponse->createResponse($this->get('jms_serializer'));
    }
}
