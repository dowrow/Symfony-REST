<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;

class SerializerController extends Controller 
{
    
    private $serializer;
    
    public function __construct() {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $this->serializer = new Serializer($normalizers, $encoders);
    }
    
    protected function formatResponse ($response, $format) {
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
    
}
