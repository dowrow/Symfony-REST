<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Court;

class CourtController extends SerializerController {
    
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
