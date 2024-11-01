<?php 
/*
Plugin Name: Wunderground Forecast Data
Plugin URI: http://turneremanager.com
Description: Search forecast data from Wunderground API
Author: Matthew M. Emma and Robert Carmosino
Version: 1.0
Author URI: http://www.turneremanager.com
*/
$WPForecastWunderground = new ForecastWunderground();

class ForecastWunderground {

  public function __construct() {
    add_action( 'wp_enqueue_scripts', array($this, 'weatherfont_style'), 10, 0  );
    add_shortcode('fw', array($this, 'wunderground_forecast'));
  }
  public function weatherfont_style() {
    wp_register_style('weatherfont', plugins_url('/css/weather-icons.css', __FILE__));
    wp_enqueue_style( 'weatherfont' );
  }
  public function wunderground_forecast( $atts ) {
    extract( shortcode_atts( array(
      'city' => 'New_York',
      'state' => 'NY',
      'days' => '3',
      'm' => 'F'
    ), $atts, 'fw' ) );
    $json_string = file_get_contents('http://api.wunderground.com/api/b8e924a8f008b81e/forecast10day/q/' . $state . '/' . $city . '.json');
    $parsed_json = json_decode($json_string);
    $forecasts = $parsed_json->{'forecast'}->{'simpleforecast'}->{'forecastday'};
    $start = 0;
    $end = $days;
    $weatherunit = '';


      for ($i = $start; $i < $end; $i++) {
          $forecast = $forecasts[$i];
          $cols = floor(100 / $days);
          if ($m === 'F') {$temp = 'High: '.$forecast->{'high'}->{'fahrenheit'}.'&deg; Low: '.$forecast->{'low'}->{'fahrenheit'}.'&deg;F';}
      if ($m === 'C') { $temp = 'High: '.$forecast->{'high'}->{'celsius'}.'&deg;C Low: '.$forecast->{'low'}->{'celsius'}.'&deg;C'; }
          
          $weatherunit .= '<div class="weatherunit" style="float: left; width: '.$cols.'%"><small><strong><center>'
          .$forecast->{'date'}->{'weekday'}.', '. substr(strstr($forecast->{'date'}->{'pretty'}, ' on '), 4).'<br><br>'
          .$this->wunderground_to_forecast_icon($forecast->{'conditions'}, 42).'<br><br>'
          .$forecast->{'conditions'}.'<br>'.$temp.'</center></small></strong></div>';
        }
     return $weatherunit;
  }

  private function wunderground_to_forecast_icon( $status, $size ) {
    $icons = array(
      'Chance of Flurries' => 'wi-day-snow',
      'Chance of Rain' => 'wi-day-rain',
      'Chance Rain' => 'wi-day-rain',
      'Chance of Freezing Rain' => 'wi-day-rain-mix',
      'Chance of Sleet' => 'wi-day-rain-mix',
      'Chance of Snow' => 'wi-day-snow',
      'Chance of Thunderstorms' => 'wi-day-thunderstorm',
      'Chance of a Thunderstorm' => 'wi-day-thunderstorm',
      'Clear' => 'wi-day-sunny',
      'Cloudy' => 'wi-day-cloudy',
      'Fog' => 'wi-smoke',
      'Haze' => 'wi-smog',
      'Mostly Cloudy' => 'wi-day-cloudy',
      'Mostly Sunny' => 'wi-day-sunny',
      'Partly Cloudy' => 'wi-day-cloudy',
      'Partly Sunny' => 'wi-day-sunny',
      'Freezing Rain' => 'wi-day-rain-mix',
      'Rain' => 'wi-rain',
      'Sleet' => 'wi-rain-mix',
      'Snow' => 'wi-snow',
      'Sunny' => 'wi-day-sunny',
      'Thunderstorms' => 'wi-thunderstorm',
      'Thunderstorm' => 'wi-thunderstorm',
      'Unknown' => 'wi-sunny',
      'Overcast' => 'wi-day-sunny-overcast',
      'Scattered Clouds' => 'wi-day-cloudy',
    );
    return '<i style="font-size: '.$size.'px;" class="wi '.$icons[$status].'"></i>';
  }
}
