<?

class GeoHelper extends WXHelpers{
  
  public function looks_like_coords($check){
    if(strstr($check, ",")){
      if(($split = explode(",", $check)) && count($split) == 2){
        if(is_numeric($split[0]) && is_numeric($split[1])) return array('lat'=>$split[0], 'lng'=>$split[1]);
      }
    }
    return false;
  }
  
  public function geo_locate($address, $key){
    if($res = looks_like_coords($address)) return $res;
    $glocal_search_url = "http://www.google.com/uds/GlocalSearch?hl=en&gss=.com&v=1.0&key=".$key;
    $url = $glocal_search_url . "&q=".urlencode($address.", uk");
    $curl = new WaxBackgroundCurl(array('url'=>$url));
    $res = json_decode($curl->fetch(),1);
    if(($lng = $res['responseData']['results'][0]['lng']) && ($lat = $res['responseData']['results'][0]['lat'])) return array('lat'=>$lat, 'lng'=>$lng);
    else return false;
  }
  
  public function os_grid_to_lat_lng($easting, $northing){
    include_once GEO_DIR."lib/coords.php";
    $os = new OSRef($easting, $northing);
    $ll = $os->toLatLng();
    $ll->OSGB36ToWGS84();
    return array('lat'=>$ll->lat, 'lng'=>$ll->lng);
  }
  
  public function bounding_box($lat, $lng, $distance_in_miles=0.5){
    include_once GEO_DIR."lib/coords.php";
    $ll = new LatLng($lat,$lng);
    return $ll->getBoundingBox($lat,$lng,$distance_in_miles);
  }
  
  public function distance_in_km($start, $end){
    return rad2deg(acos(sin(deg2rad($start['lat'])) * sin(deg2rad($end['lat'])) +
           cos(deg2rad($start['lat'])) * cos(deg2rad($end['lat'])) *
           cos(deg2rad($start['lng'] - $end['lng'])))) * 60 * 1.1515 * 1.609344;
  }
  
  public function distancesort($a,$b){
    if($a['distance'] == $b['distance']) return 0;
    return ($a['distance'] < $b['distance']) ? -1 : 1;
  }
}

?>