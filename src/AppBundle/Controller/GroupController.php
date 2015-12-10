<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use AppBundle\Entity\Group;

class GroupController extends Controller
{
    private $serializer;
    
    public function __construct() {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $this->serializer = new Serializer($normalizers, $encoders);
    }
    
    private function formatResponse ($response, $format) {
        if (empty($response)) {
            $response = "error";
        }
        if ($format === 'xml') {
            $xmlContent = $this->serializer->serialize($response, $format);
            return new Response($xmlContent, Response::HTTP_OK, array(
                'Content-type' => 'text/xml'
            ));
        } else {
            $jsonContent = $this->serializer->serialize($response, 'json');
            return new Response($jsonContent, Response::HTTP_OK, array(
                'Content-type' => 'application/json'
            ));
        }
    }
    
    public function getAction($id, $format) {
        $em = $this->getDoctrine()->getManager();
        $group = $em->getRepository('AppBundle:Group')->find(intval($id));
        return $this->formatResponse($group, $format);
    }
    
    public function deleteAction($id, $format) {
        $em = $this->getDoctrine()->getManager();
        $group = $em->getRepository('AppBundle:Group')->find(intval($id));
        $em->remove($group);
        $em->flush();
        return $this->formatResponse("ok", $format);
    }
    
    public function modifyAction($id, $format) {
        try{
            $em = $this->getDoctrine()->getManager();
            $group = $em->getRepository('AppBundle:Group')->find(intval($id));
            $params = array();
            $content = $this->get("request")->getContent();
            if (!empty($content)) {
                $params = json_decode($content, true);
                $group->setName($params['name']);
                $group->setRoles($params['roles']);     
            }
            $em->flush();
            return $this->formatResponse("ok", $format);
        } catch (Exception $ex) {
            return $this->formatResponse("error", $format);
        }
    }
    
    public function getAllAction($format) {
        $em = $this->getDoctrine()->getManager();
        $groups = $em->getRepository('AppBundle:Group')->findAll();
        return $this->formatResponse($groups, $format);
    }
    
    public function createAction($format) {
        try {
            $em = $this->getDoctrine()->getManager();
            $group = new Group();
            $params = array();
            $content = $this->get("request")->getContent();
            if (!empty($content)) {
                $params = json_decode($content, true);
                $group->setName($params['name']);
                $group->setRoles($params['roles']);
            }
            $em->persist($group);
            $em->flush();
            return $this->formatResponse("ok", $format);
        } catch (Exception $ex) {
            return $this->formatResponse("error", $format);
        }
    }
    
    public function addUserAction($id, $userId, $format) {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->find(intval($userId));
        $group = $em->getRepository('AppBundle:Group')->find(intval($id));
        if (!empty($user) && !empty($group)) {
            $user->addGroup($group);
            $group->addUser($user);
        }
        $em->flush();
        return $this->formatResponse("ok", $format);
    }
}
