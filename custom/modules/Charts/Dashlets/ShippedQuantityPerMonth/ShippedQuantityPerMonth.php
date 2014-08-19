<?php

require_once('custom/include/Dashlets/DashletGenericBarChart.php');

class ShippedQuantityPerMonth extends DashletGenericBarChart
{

    protected function getDataset()
    {
        include 'custom/include/magentoSoapIntegration/config.php';
        try {
            $SoapResponse = $soap->call($session_id, 'sales_order.getMonthlyShippedQtys');
        } catch (Exception $e) {
            $SoapResponse = array();
        }
        $mappingMonths = array(
            '1' => 'Jan',
            '2' => 'Feb',
            '3' => 'March',
            '4' => 'April',
            '5' => 'May',
            '6' => 'June',
            '7' => 'July',
            '8' => 'Aug',
            '9' => 'Sept',
            '10' => 'Oct',
            '11' => 'Nov',
            '12' => 'Dec',
        );
        foreach ($SoapResponse as $key => $value) {
            $returnArray[$mappingMonths[$key]] = $value;
        }
        return $returnArray;
    }

}

?>