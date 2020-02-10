<?php

namespace Fly\PlatformBundle\Service;

use Doctrine\ORM\EntityManager;
use Fly\PlatformBundle\Entity\FlyCache;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\TwigBundle\TwigEngine;

class FlyService
{


    private $em;
    private $templating;
    private $url;
    private $user;
    private $password;



    public function __construct(EntityManager $em, TwigEngine $templating, $url, $user, $password)
    {
        $this->em = $em;
        $this->templating = $templating;
        $this->user = $user;
        $this->url = $url;
        $this->password = $password;

    }


    public function lowFareSearch($searchParams)
    {
        $searchData = [
            'asc'=>'success',
            'msg'=>null,
            'data'=>null,
            'cached'=>false,
        ];



        $recommendations = [];
        $outbonds = [];
        $inbounds = [];


        //check FlyCache;
        $cacheData = $this->em->getRepository('FlyPlatformBundle:FlyCache')->checkCacheData(
            $searchParams['departureAid'],
            $searchParams['arrivalAid'],
            \DateTime::createFromFormat('Y-m-d H:i:s', $searchParams['departureDate'].' 00:00:00'),
            \DateTime::createFromFormat('Y-m-d H:i:s', $searchParams['arrivalDate'].' 00:00:00')
        );

        if($cacheData){

            $recommendations =  $cacheData['recommendations'];
            $outbonds = $cacheData['outbounds'];
            $inbounds = $cacheData['inbounds'];
            $searchData['cached'] = true;

        }else{


            $res = $this->soapRequest($searchParams);
            //check errors
            if(!$res['errors']){

                $response = $res['response'];
                $response = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $response);
                $response = str_ireplace(['ns2:'], '', $response);
                $parser = simplexml_load_string($response);

                $json = json_encode($parser);
                $array = json_decode($json,TRUE);
                $res['response'] = $array;
            }

        // check errors
        if($res['errors']){
            $searchData['asc'] = 'error';
            $searchData['msg'] = $res['errors'];

            return $searchData;
        }

        // if no errors prepare data



//dump($cacheData);die;



            $lowFareSearch = $res['response']['Body']['lowFareSearchResponse'];
//dump($lowFareSearch,$json, $searchParams);die();

            if(!count($lowFareSearch['recommendations'])){
                $searchData['asc'] = 'error';
                $searchData['msg'] = "Nothing found";
                return $searchData;
            }
            $recommendations = $lowFareSearch['recommendations']['recommendation'];
            $outbonds = $lowFareSearch['originDestinations']['originDestination'][0]['itineraries']['itinerary'];
            $inbounds = $lowFareSearch['originDestinations']['originDestination'][1]['itineraries']['itinerary'];
        }





        if(count($recommendations) && !$cacheData){
            $cache = new FlyCache();
            $cache->setRecommendation($recommendations);
            $cache->setOutbound($outbonds);
            $cache->setInbound($inbounds);
            $cache->setAirportOne($searchParams['departureAid']);
            $cache->setAirportTwo($searchParams['arrivalAid']);
            $cache->setDepartureOut(\DateTime::createFromFormat('Y-m-d H:i:s', $searchParams['departureDate'].' 00:00:00'));
            $cache->setDepartureIn(\DateTime::createFromFormat('Y-m-d H:i:s', $searchParams['arrivalDate']. ' 00:00:00'));
            $this->em->persist($cache);
            $this->em->flush();
        }

        $flyData = [];
        foreach($recommendations as $rec){
            $flyRow = [
                'price'=>null,
                'duration'=>null,
                'airline'=>null,
                'outboundsSummary' => [
                    'start'=>null,
                    'end'=>null,
                    'airports'=>null,
                    'marketingAirlineCode'=>null,
                    'operatingAirlineCode'=>null,
                    'stops'=>[
//                        'time'=>null,
//                        'airport'=>null,
                    ]
                ],
                'inboundsSummary' => [
                    'start'=>null,
                    'end'=>null,
                    'airports'=>null,
                    'marketingAirlineCode'=>null,
                    'operatingAirlineCode'=>null,
                    'stops'=>[
//                        'time'=>null,
//                        'airport'=>null,
                    ]
                ],
                'outbounds' =>[
//                    'duration'=>null, 'depTime'=>null, 'arrTime'=>null, 'stops'=>null
                ],
                'inbounds' =>[
//                    'duration'=>null, 'depTime'=>null, 'arrTime'=>null, 'stops'=>null
                ],
            ];
            $outId = $rec['itineraries']['itineraryId'][0];
            $inId = $rec['itineraries']['itineraryId'][1];

            $outB = $this->getItinerarieById($outId,$outbonds);
            $inB = $this->getItinerarieById($inId,$inbounds);

            $flyRow['price'] = $rec['pricingDetail']['baseAmount']['@attributes']['amount'] .' '. $rec['pricingDetail']['baseAmount']['@attributes']['currencyCode'];
            $flyRow['airline'] =$rec['validatingAirlines']['airlineCode'];

            $outDur = new \DateInterval($outB['@attributes']['duration']);
            $inDur = new \DateInterval($inB['@attributes']['duration']);
            $flyRow['outboundsSummary']['duration'] = $outDur->format('%hh %Imin');
            $flyRow['inboundsSummary']['duration'] = $inDur->format('%hh %Imin');

            // outbounds
            if(isset($outB['segments']['segment']) && count($outB['segments']['segment'])){

               if(isset($outB['segments']['segment']['@attributes'])){
                   $item = $outB['segments']['segment'];
                   $flyRow['outbounds'][] = $this->createItinerarieItem($outB,$item,$flyRow);
               }else{
                   foreach($outB['segments']['segment'] as $item){
                       $flyRow['outbounds'][] = $this->createItinerarieItem($outB,$item,$flyRow);
                   }
               }
            }

            // inbounds
            if(isset($inB['segments']['segment']) && count($inB['segments']['segment'])){

                if(isset($inB['segments']['segment']['@attributes'])){
                    $item = $outB['segments']['segment'];
                    $flyRow['inbounds'][] = $this->createItinerarieItem($inB,$item,$flyRow);
                }else{
                    foreach($inB['segments']['segment'] as $item){
                        $flyRow['inbounds'][] = $this->createItinerarieItem($inB,$item,$flyRow);
                    }
                }
            }


            $flyData[] = $flyRow;

        }



        // rescan and summary for Outbounds and Inbounds


        //OUTBOUNDS
        foreach($flyData as $k => $item){

            $cnt = 0;
            $lastArrivalDate = null;
            foreach($item['outbounds'] as $outbound){

                //$lastArrivalDate = $outbound['arrival']['date'];
                // start Date - end Date
                if($cnt == 0){
                    $flyData[$k]['outboundsSummary']['start'] = new \DateTime($outbound['departure']['date']);
                }

                if($cnt == count($item['outbounds']) - 1 ){
                    $flyData[$k]['outboundsSummary']['end'] = new \DateTime($outbound['arrival']['date']);
                }

                // Airports

                if(count($item['outbounds']) == 1){ // if only 1 route
                    $flyData[$k]['outboundsSummary']['airports'][] = $outbound['departure']['airportCode'];
                    $flyData[$k]['outboundsSummary']['airports'][] = $outbound['arrival']['airportCode'];
                }
                if(count($item['outbounds']) > 1){ // if more then 1 route
                    if($cnt == 0){
                        $flyData[$k]['outboundsSummary']['airports'][] = $outbound['departure']['airportCode'] ;
                        $lastArrivalDate = new \DateTime($outbound['arrival']['date']);
                    }
                    if($cnt > 0 && $cnt < count($item['outbounds']) ){
                        $lastDepartureDate = new \DateTime($outbound['departure']['date']);
                        $stopInterval = $lastDepartureDate->diff($lastArrivalDate);

                        $flyData[$k]['outboundsSummary']['airports'][] = $outbound['departure']['airportCode'] ;
                        $flyData[$k]['outboundsSummary']['stops'][] = [
                            'airport'=>$outbound['departure']['airportCode'],
                            'time'=>[
                                'arrival'=>$lastArrivalDate,
                                'departure'=>$lastDepartureDate,
                                'interval' => $stopInterval->format('%hh %Im'),
                            ]
                        ];
                    }
                    if($cnt == count($item['outbounds']) - 1){
                        $flyData[$k]['outboundsSummary']['airports'][] = $outbound['arrival']['airportCode'] ;
                    }

                }

                // operator&marketing
                $flyData[$k]['outboundsSummary']['marketingAirlineCode'] = $outbound['marketingAirlineCode'];
                $flyData[$k]['outboundsSummary']['operatingAirlineCode'] = $outbound['operatingAirlineCode'];


//                $flyData[$k]['outboundsSummary']['airlines'] .= $outbound['operatingAirlineCode']

                $cnt++;
            }

            // INBOUNDS
            $cnt = 0;
            foreach($item['inbounds'] as $outbound){


                // start Date - end Date
                if($cnt == 0){
                    $flyData[$k]['inboundsSummary']['start'] = new \DateTime($outbound['departure']['date']);
                }

                if($cnt == count($item['inbounds']) - 1 ){
                    $flyData[$k]['inboundsSummary']['end'] = new \DateTime($outbound['arrival']['date']);
                }

                // Airports
                if(count($item['inbounds']) == 1){ // if only 1 route
                    $flyData[$k]['inboundsSummary']['airports'][] = $outbound['departure']['airportCode'];
                    $flyData[$k]['inboundsSummary']['airports'][] = $outbound['arrival']['airportCode'];
                }
                if(count($item['inbounds']) > 1){ // if more then 1 route
                    if($cnt == 0){
                        $flyData[$k]['inboundsSummary']['airports'][] = $outbound['departure']['airportCode'] ;
                        $lastArrivalDate = new \DateTime($outbound['arrival']['date']);
                    }
                    if($cnt > 0 && $cnt < count($item['inbounds']) ){
                        $lastDepartureDate = new \DateTime($outbound['departure']['date']);
                        $stopInterval = $lastDepartureDate->diff($lastArrivalDate);

                        $flyData[$k]['inboundsSummary']['airports'][] = $outbound['departure']['airportCode'] ;
                        $flyData[$k]['inboundsSummary']['stops'][] = [
                            'airport'=>$outbound['departure']['airportCode'],
                            'time'=>[
                                'arrival'=>$lastArrivalDate,
                                'departure'=>$lastDepartureDate,
                                'interval' => $stopInterval->format('%hh %Im'),
                            ]
                        ];
                    }
                    if($cnt == count($item['inbounds']) - 1){
                        $flyData[$k]['inboundsSummary']['airports'][] = $outbound['arrival']['airportCode'] ;
                    }

                }

                // operator&marketing
                $flyData[$k]['inboundsSummary']['marketingAirlineCode'] = $outbound['marketingAirlineCode'];
                $flyData[$k]['inboundsSummary']['operatingAirlineCode'] = $outbound['operatingAirlineCode'];


//                $flyData[$k]['outboundsSummary']['airlines'] .= $outbound['operatingAirlineCode']

                $cnt++;
            }

        }

//dump($flyData);
//        die;

        $searchData['flyData'] = $flyData;
        $searchData['data'] = [
            'outbounds' => $outbonds,
            'inbounds' => $inbounds,
            'recommendations' => $recommendations,
        ];

        return $searchData ;
    }


    protected function soapRequest($searchParams)
    {
//        dump($searchParams);die();
        $res = ['response'=>null, 'errors'=>null];

        $url = $this->url;
        $user = $this->user;  //  username
        $password = $this->password; // password

        $xml_post_string =  $this->templating->render('default/soap/request.html.twig',['user'=>$user,'password'=>$password,'searchParams'=>$searchParams]);   // data from the form, e.g. some ID number
        $headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
//            "SOAPAction: http://connecting.website.com/WSDL_Service/GetPrice",
            "Content-length: ".strlen($xml_post_string),
        ); //SOAPAction: your op URL



        $ch = curl_init($url);
//        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_post_string");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $res['response'] = curl_exec($ch);
        $res['errors'] = curl_error($ch);
        curl_close($ch);

        return $res;

    }

    protected function getItinerarieById($id,$arr)
    {
        foreach($arr as $item){
            if($item['@attributes']['id'] == $id){
                return $item;
            }
        }

        return null;
    }

    protected function createItinerarieItem($int,$item, &$flyRow)
    {
        if(!isset($item["@attributes"])){
            if(isset($item[0]["@attributes"]))
            $item = $item[0];
            dump($item);
        }
//        dump($item["@attributes"]);die;
        $departureDateTime = $item["@attributes"]['departureDateTime'];
        $arrivalDateTime = $item["@attributes"]['arrivalDateTime'];
        $marketingAirlineCode = $item["@attributes"]['marketingAirlineCode'];
        $operatingAirlineCode = $item["@attributes"]['operatingAirlineCode'];

        $departureAirportCityCode = $item['departureAirport']["@attributes"]['cityCode'];
        $departureAirportCode = $item['departureAirport']["@attributes"]['code'];

        $arrivalAirportCityCode = $item['arrivalAirport']["@attributes"]['cityCode'];
        $arrivalAirportCode = $item['arrivalAirport']["@attributes"]['code'];

//        $flyRow['duration'] = $int['@attributes']['duration'];

        $res = [
//            'duration'=>$int['@attributes']['duration'],
            'marketingAirlineCode'=>$marketingAirlineCode,
            'operatingAirlineCode' => $operatingAirlineCode,
            'departure' => [
                'date' => $departureDateTime,
                'cityCode' => $departureAirportCityCode,
                'airportCode' => $departureAirportCode,
            ],
            'arrival' => [
                'date' => $arrivalDateTime,
                'cityCode' => $arrivalAirportCityCode,
                'airportCode' => $arrivalAirportCode,
            ]
        ];

        return $res;
    }


}