<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use AppBundle\Entity\Court;

class CourtController extends Controller
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
        $court = $em->getRepository('AppBundle:Court')->find(intval($id));
        return $this->formatResponse($court, $format);
    }
    
    public function deleteAction($id, $format) {
        $em = $this->getDoctrine()->getManager();
        $court = $em->getRepository('AppBundle:Court')->find(intval($id));
        $em->remove($court);
        $em->flush();
        return $this->formatResponse("ok", $format);
    }
    
    public function modifyAction($id, $format) {
        try {
            $em = $this->getDoctrine()->getManager();
            $court = $em->getRepository('AppBundle:Court')->find(intval($id));
            $params = array();
            $content = $this->get("request")->getContent();
            if (!empty($content)) {
                $params = json_decode($content, true);
                $court->setActive($params['active']);
            }
            $em->flush();
            return $this->formatResponse("ok", $format);
        } catch (Exception $ex) {
            return $this->formatResponse("error", $format);
        }
    }
    
    public function getAllAction($format) {
        $em = $this->getDoctrine()->getManager();
        $courts = $em->getRepository('AppBundle:Court')->findAll();
        return $this->formatResponse($courts, $format);
    }
    
    public function createAction($format) {
        try{
            $em = $this->getDoctrine()->getManager();
            $court = new Court();
            $params = array();
            $content = $this->get("request")->getContent();
            if (!empty($content)) {
                $params = json_decode($content, true);
                $court->setActive($params['active']);
            }
            $em->persist($court);
            $em->flush();
            return $this->formatResponse("ok", $format);
        } catch (Exception $ex) {
            return $this->formatResponse("error", $format);
        }
    }
}
