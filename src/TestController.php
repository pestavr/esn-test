<?php

namespace EsnTest;

use EsnTest\Controller\EsnFetcher;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
/**
 * Class for the exercise.
 */
class TestController extends EsnFetcher {

  /**
   * Returns the needed data for solving task 4.
   *
   * You might need extra functions for this test.
   *
   * @return 
   *   The data you decide to return.
   */
  public function getDataT4() {
    //Retreiving Data from API Endpoints
    $countries = $this->getRequest('countries');
    $sections = $this->getRequest('sections');
    $resp = [];
    //Json to Array
    $sections = json_decode($sections, TRUE);
    $countries = json_decode($countries, TRUE);
    //sorting countries array
    array_multisort(array_column($countries, 'country'), SORT_ASC, $countries);

    foreach ($countries as $country) {
      foreach ($sections as $section) {
        //Adding Setions to each country
        if ($section['country'] === $country['country']){
          $country['sections'][] = $section;
        }
      }
      //Counting Section for each country
      $country['num_of_sections'] = isset($country['sections']) && $country['sections'] ? count($country['sections']) : 0;
      $resp[] = $country;
    }
    
    return $resp;
  }

  /**
   * Returns the needed data for solving task 5.
   *
   * You might need extra functions for this test.
   *
   * @return 
   *   The data you decide to return.
   */
  public function getDataT5() {
    /**
     * Reading Xlsx
     */
    $file = "codes.xlsx";
    $reader = new Xlsx();
    $reader->setReadDataOnly(true);  
    $spreadsheet = $reader->load($file);
    $worksheet = $spreadsheet->getActiveSheet()->toArray();
    $resp = [];
    /**
     * Get Sections from API 
     */
    $sections = $this->getRequest('sections');
    $sections = json_decode($sections, TRUE);
    /**
     * Seperate Required Columns
     */
    $sectionCodes = array_column($sections, 'code');
    $sectionNames = array_column($sections, 'label');
    /**
     * Create new Array with key Section Code
     * and Value Section Name
     */
    $sections = array_combine($sectionCodes, $sectionNames);
    $resp['sections'] = $sections; 
    /**
     * Request Data for each Code from the API
     */
    $codes = array_merge(...$worksheet); //flatten worksheet array 
    sort($codes); // sorting by code
    foreach ($codes as $code) {
      if (isset($code) && $code !== 'Code'){
        $response = $this->getCardData($code);
        $row['code'] = $code;
        //if response is not empty the store data
        if (!empty($response)){
          $row['data'] = $response;
        }
        
        $resp['cards'][] = $row;
      }
      
    }
    return $resp;
  }

}
