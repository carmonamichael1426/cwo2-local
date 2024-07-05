<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DeductionReportController extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('upload');
        $this->load->library('Pdf');
        $this->load->model('deductionreportmodel');
        $this->load->library('session');
        $this->load->library('form_validation');
        date_default_timezone_set('Asia/Manila');


        //Disable Cache
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        ('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }

    public function generateDeductionReport()
    {
        $fetch_data = $this->input->post(NULL,TRUE);       

        if( !empty($fetch_data['dateFrom']) || !empty($fetch_data['dateTo']) ){

            if( $fetch_data['searchBy'] == 'All Supplier' ){
                $report    = $this->deductionreportmodel->getDeductionReport('All Supplier',$fetch_data['dedtype'],NULL,$fetch_data['dateFrom'],$fetch_data['dateTo']);  
            } else if( $fetch_data['searchBy'] == 'Supplier' ){
                $report    = $this->deductionreportmodel->getDeductionReport('Supplier',$fetch_data['dedtype'],$fetch_data['supplierSelect'],$fetch_data['dateFrom'],$fetch_data['dateTo']);  
            }


            if( !empty( $report) ){

                $generate = $this->printReport($report,$fetch_data['dedtype'],$fetch_data['dateFrom'],$fetch_data['dateTo']);
                $msg = ['info' => 'success', 'message' => 'Report is ready!', 'file' => $generate];

            } else {

                $msg = ['info' => 'no-data', 'message' => 'No data to generate!'];
            }  

           
        }  else {

            $msg = ['info' => 'no-data', 'message' => 'No data to generate!'];

        }
        
        JSONResponse($msg);

     
    }


    private function printReport($data,$type,$from,$to)
    {
        $dateFrom     = date("M d, Y", strtotime($from));
        $dateTo       = date("M d, Y", strtotime($to));
        $typeId       = $type;

        foreach($data as $a){
            $suppliers[]  = $a['supplier_id'];  
            $deductions[] = $a['deduction_type_id'] ;
            $sop[]        = $a['sop_id'] ;
        }  
        
        $supplierCount    = array_count_values($suppliers);
        $uniqueDeductions = array_unique($deductions);
        $uniqueSop        = array_unique($sop);

        $deductionTotal = 0;        
        $previousSupId  = "" ;
        $previousType   = "" ;
        $previousSop    = "" ;

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Mariel Taray');
        $pdf->SetTitle('Deduction Report');
        $pdf->SetSubject('CWO-SOP Deduction Report');
        $pdf->SetKeywords('CWO, SOP, Deduction, Report');

        // set default header data
        $pdf->SetHeaderData('alturas.png', 32, '' . '', 'Cash With Order Monitoring Report');

        // set header and footer fonts
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(17, 20, 15);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        $pdf->AddPage('L', array(215.9,355.6));      
        $pdf->SetDisplayMode('fullpage'); 

        $pdf->SetFont('helvetica', '', 9);

        if($typeId == 'All'){
            $html = '<div>
                     <span style="font-size: large;padding-left:20px">SUMMARY OF PAYMENT - DEDUCTION REPORT</span><br>
                     <span style="font-size: medium;padding-left:20px">From : '.$dateFrom.' To : '.$dateTo.'</span><br><br>
                     </div>
                    
                     <table border="1" >
                        <tr>
                            <th colspan="2" align="center" bgcolor="#000000" color="white">SUPPLIER</th>
                            <th align="center" bgcolor="#000000" color="white">SOP NO</th>
                            <th align="center" bgcolor="#000000" color="white">SOP DATE</th>
                            <th align="center" bgcolor="#000000" color="white">DEDUCTION TYPE</th>
                            <th colspan="2" align="center" bgcolor="#000000" color="white">DEDUCTION DESCRIPTION</th>
                            <th align="center" bgcolor="#000000" color="white">AMOUNT</th>
                        </tr>';
            
            

            foreach($supplierCount as $supId => $count){

                $rowspan = 0;
                foreach( $uniqueSop as $sopIdd ){
                    foreach( $uniqueDeductions as $uIdd ){
                        foreach( $data as $bb ){ 
                            if( $supId == $bb['supplier_id'] ){
                                if( $sopIdd == $bb['sop_id'] ){
                                    if( $uIdd == $bb['deduction_type_id'] ){
                                        if( $bb['supplier_id'] !== $previousSupId ){
                                            $rowspan++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                foreach( $uniqueSop as $sopId ){                
                    
                    $sopCount   = $this->deductionreportmodel->countSop($supId,$sopId)['scount'];

                    foreach( $uniqueDeductions as $uId ){
                        $deductionCount = $this->deductionreportmodel->countDeductions($sopId,$uId)['dcount'];
                        foreach( $data as $b ){ 

                            if( $supId == $b['supplier_id'] ){
                                if( $sopId == $b['sop_id'] ){
                                    if( $uId == $b['deduction_type_id'] ){

                                        if( $b['supplier_id'] !== $previousSupId ){

                                            $html .= '<tr>
                                                        <td rowspan = "'.$rowspan.'" colspan="2" align="center">
                                                        '.$b['supplier_name']. 
                                                        '</td>
                                                        <td rowspan = "'.$sopCount.'" align="center">'.$b['sop_no'] .'</td>
                                                        <td rowspan = "'.$sopCount.'" align="center">'.date("Y-m-d", strtotime($b['sop_date'])) .'</td>
                                                        <td rowspan = "'.$deductionCount.'" align="center">'.$b['type'].'</td>
                                                        <td colspan="2" align="center">'.$b['description'].'</td>
                                                        <td align="right">'.number_format($b['deduction_amount'],2) .'</td>
                                                    </tr>';
                                        

                                        } else if ( $b['supplier_id'] === $previousSupId ){

                                            if( $b['sop_id'] === $previousSop ){

                                                if( $b['deduction_type_id'] === $previousType ){

                                                    $html .= '<tr>
                                                                <td colspan="2" align="center">'.$b['description'] .'</td>
                                                                <td align="right">'. number_format($b['deduction_amount'],2).'</td> 
                                                            </tr>';

                                                } else {
                                                    $html .= '<tr>
                                                                <td rowspan = "'.$deductionCount.'" align="center">'.$b['type'] .'</td>
                                                                <td colspan="2" align="center">'.$b['description'].'</td>
                                                                <td align="right">'. number_format($b['deduction_amount'],2).'</td> 
                                                            </tr>';
                                                }
                                            

                                            } else {

                                                $html .= '<tr>
                                                            <td rowspan = "'.$sopCount.'" align="center">'.$b['sop_no'].'</td>
                                                            <td rowspan = "'.$sopCount.'" align="center">'.date("Y-m-d", strtotime($b['sop_date'])).'</td>
                                                            <td rowspan = "'.$deductionCount.'" align="center">'.$b['type'].'</td>
                                                            <td colspan="2" align="center">'.$b['description'].'</td>
                                                            <td align="right">'. number_format($b['deduction_amount'],2).'</td> 
                                                        </tr>';

                                            }
                                        }

                                        $deductionTotal += $b['deduction_amount'];
                                        $previousSupId   = $b['supplier_id'] ;  
                                        $previousType    = $b['deduction_type_id'];
                                        $previousSop     = $b['sop_id'];
                                        $pdf->Ln();
                                    }
                                }
                            }
                        }                
                    }
                }   
            }
        } else {

            $html = '                  
                    <div>
                    <span style="font-size: large;padding-left:20px">SUMMARY OF PAYMENT - DEDUCTION REPORT</span><br>
                    <span style="font-size: medium;padding-left:20px">From : '.$dateFrom.' To : '.$dateTo.'</span><br><br>
                    </div>
                    
                    
                    <table border="1" >
                        <tr>
                            <th colspan="2" align="center" bgcolor="#000000" color="white">SUPPLIER</th>
                            <th align="center" bgcolor="#000000" color="white">DEDUCTION TYPE</th>
                            <th align="center" bgcolor="#000000" color="white">SOP NO</th>
                            <th align="center" bgcolor="#000000" color="white">SOP DATE</th>                   
                            <th colspan="2" align="center" bgcolor="#000000" color="white">DEDUCTION DESCRIPTION</th>
                            <th align="center" bgcolor="#000000" color="white">AMOUNT</th>
                        </tr>';

            foreach($supplierCount as $supId => $count){

                $rowspan = 0;
                foreach( $uniqueSop as $sopIdd ){
                    foreach( $data as $bb ){ 
                        if( $supId == $bb['supplier_id'] ){
                            if( $sopIdd == $bb['sop_id'] ){
                                if( $bb['supplier_id'] !== $previousSupId ){
                                    $rowspan++;
                                }                                
                            }
                        }
                    }
                }

                foreach( $uniqueSop as $sopId ){                
                     
                    $deductionCount = $this->deductionreportmodel->countDeductions($sopId,$typeId)['dcount'];

                    foreach( $data as $b ){ 

                        if( $supId == $b['supplier_id'] ){
                            if( $sopId == $b['sop_id'] ){

                                    if( $b['supplier_id'] !== $previousSupId ){
                                        $html .= '<tr>
                                                    <td rowspan = "'.$rowspan.'" colspan="2" align="center">
                                                    '.$b['supplier_name']. 
                                                    '</td>
                                                    <td rowspan = "'.$rowspan.'" align="center">'.$b['type'].'</td>
                                                    <td rowspan = "'.$deductionCount.'" align="center">'.$b['sop_no'] .'</td>
                                                    <td rowspan = "'.$deductionCount.'" align="center">'.date("Y-m-d", strtotime($b['sop_date'])) .'</td>                                                    
                                                    <td colspan="2" align="center">'.$b['description'].'</td>
                                                    <td align="right">'.number_format($b['deduction_amount'],2) .'</td>
                                                </tr>';
                                    

                                    } else if ( $b['supplier_id'] === $previousSupId ){

                                        if( $b['sop_id'] === $previousSop ){

                                            $html .= '<tr>
                                                        <td colspan="2" align="center">'.$b['description'] .'</td>
                                                        <td align="right">'. number_format($b['deduction_amount'],2).'</td> 
                                                    </tr>';
                                        } else {
                                            $html .= '<tr>
                                                        <td rowspan = "'.$deductionCount.'" align="center">'.$b['sop_no'].'</td>
                                                        <td rowspan = "'.$deductionCount.'" align="center">'.date("Y-m-d", strtotime($b['sop_date'])).'</td>
                                                        <td colspan="2" align="center">'.$b['description'].'</td>
                                                        <td align="right">'. number_format($b['deduction_amount'],2).'</td> 
                                                    </tr>';

                                        }
                                    }

                                    $deductionTotal += $b['deduction_amount'];
                                    $previousSupId   = $b['supplier_id'] ;  
                                    $previousSop     = $b['sop_id'];

                            }
                        }
                    }                
                        
                }               
                
            }
        }
        

        $html .= '<tfoot>   
                    <tr>
                        <td colspan="7" align="left" style=" font-weight: bold">TOTAL DEDUCTION AMOUNT : </td>
                        <td align="right" style="font-weight: bold;">'. number_format($deductionTotal,2).'</td> 
                    </tr>
                  </tfoot>
                </table>';
        $pdf->writeHTML($html, true, false, true, false, '');
        $fileName = 'SOP_DEDUCTION_REPORT_'.time().'.pdf';
        $pdf->Output(getcwd() .'/files/Reports/DeductionReports/' . $fileName, 'F');

        return $fileName ;
    }
   


   
}
