<?php

function JSONResponse($data)
{
    header('Content-Type: application/json');
    die(json_encode($data));
}

function getSequenceNo(array $seq, array $option, $useNext = false)
{
    $seq = (object) $seq;
    $option = (object) $option;

    $CI = &get_instance();
    $db = &$CI->db;


    $query = $db->query("
        SELECT  
            *, 
            concat( `code`, lpad(`number`, `lpad`, `pad_string`)) as sequence 
        FROM 
            sequence
        WHERE
            code = '$seq->code'
        LIMIT 
            1
    ");


    if ($query->num_rows() == 0) {
        unset($seq->table);

        $db->insert('sequence', (array)$seq);
        $seq->sequence = $seq->code . str_pad($seq->number, $seq->lpad, $seq->pad_string, STR_PAD_LEFT);
    } else {
        $seq = $query->row();

        $seq->number++;
        $seq->sequence = $seq->code . str_pad($seq->number, $seq->lpad, $seq->pad_string, STR_PAD_LEFT);
    }


    $update_result = true;


    do {
        $existing = true;
        while ($existing) {
            $fquery = $db->query("
                SELECT  
                    *    
                FROM 
                    $option->table
                WHERE
                    $option->column = '$seq->sequence'
            ");

            if ($fquery->num_rows() == 0) {
                $existing = false;

                if (!$useNext) {
                    return $seq->sequence;
                }
            } else {

                if (!$useNext) {
                    $db->update('sequence', ['number' => $seq->number], ['code' => $seq->code]);
                }

                $seq->number++;
                $seq->sequence = $seq->code . str_pad($seq->number, $seq->lpad, $seq->pad_string, STR_PAD_LEFT);
            }
        }

        $db->update('sequence', ['number' => $seq->number], ['code' => $seq->code]);

        if ($useNext && $db->affected_rows() == 0) {
            $update_result = false;
            $seq->number++;
            $seq->sequence = $seq->code . str_pad($seq->number, $seq->lpad, $seq->pad_string, STR_PAD_LEFT);
        } else {
            $update_result = true;
        }
    } while (!$update_result);

    return $seq->sequence;
}

function backToGrossSop($supId, $amount, $disc1, $disc2, $disc3, $disc4, $disc5,$disc6, $vat,$noOfDisc = NULL)
{
    $gross = 0;
    if ($supId == 1) { //ALASKA
        $gross = $amount / $disc1 /$disc2;
    } else if ($supId == 2) { //js unitrade /7%/4%
        $gross = $amount * $vat / $disc1 / $disc2;
    } else if ($supId == 5) { //intelligent /10%/10%/4% * VAT        
        $gross =  $amount * $vat  / $disc1 / $disc2 / $disc3; //$amount * 1.12  / 0.90 / 0.90 / 0.96  ; 
    } else if ($supId == 9) { //mondelez 6%or7% * VAT
        $gross = ($amount / $disc1) * $vat;
    } else if ($supId == 14) { //VALIANT
        $gross = ($amount / $disc1) * $vat;
    } else if ($supId == 13) { //ACS
        $gross = $amount;
    } else if ($supId == 3) { //COSMETIQUE
        $gross = $amount / $disc1 / $disc2 / $disc3;
    } else if ($supId == 10) { //SUYEN
        $gross = $amount / $disc1;
    } else if ($supId == 16) { //SCPG   
        $gross = ($amount / $disc1) * $vat;
    } else if ($supId == 15) { //MCKENZIE
        $gross = ($amount / $disc1) * $vat;
    } else if ($supId == 49) { //ALECO
        $gross = $amount;
    } else if ($supId == 20) { //BIG E
        $gross = $amount;
    } else if ($supId == 4) { //ECO
        $gross = $amount;
    } else if ($supId == 23) { //FOOD INDUSTRIES
        $gross = $amount;
    } else if ($supId == 22) { //FOOD CHOICE
        // $gross = $amount / $disc1;
        $gross = $amount ;
    } else if ($supId == 55) { //GSMI
        $gross = $amount;
    } else if ($supId == 24) { //GREEN CROSS
        $gross = $amount;
    } else if ($supId == 8) { //MEAD JOHNSON
        // $gross = ($amount / $disc1) * $vat ;
        $gross = $amount / $disc1 ;
    } else if ($supId == 25){  //KSK        
        $gross = $amount;
    } else if ($supId == 26){  //RECKIT        
        $gross = ($amount / $disc1) * $vat;
    } else if ($supId == 12){  //PEERLESS        
        $gross = $amount ;
    } else if ($supId == 27){ //CLE ACE
        if($noOfDisc == '3'){
            $gross = ($amount / $disc1 / $disc2 / $disc3) * $vat ;
        } else if ($noOfDisc == '4'){
            $gross = ($amount / $disc1 / $disc2 / $disc3 / $disc4) * $vat ;
        }        
    } else if ($supId == 28){ //KAREILA
        $gross = $amount / $disc1 ;
    } else if ($supId == 29){ //RUFINA
        $gross = $amount ;
    } else if ($supId == 31){ //KETTLE
        $gross = $amount ;
    } else if ($supId == 32){ //RPO FINE FOODS
        $gross = $amount ;
    } else if ($supId == 33){ //MARYLAND
        $gross = $amount ;
    } else if ($supId == 34){ //MIRAMAR
        // $gross = $amount ; //sauna nga formula
        $gross = $amount / $disc1 ; // new formula net of 10% but gross of 2% and gross of vat
    } else if ($supId == 35){ //NUTRITIVE
        $gross = $amount ;
    } else if ($supId == 36){ //STATELINE SNACK FOOD CORP
            $gross = $amount ;
    } else if ($supId == 37){ //NATION CONFECTIONERY
        $gross = $amount ;
    } else if ($supId == 38){ //HOWDEE AND YUMEE FOOD CORP
        $gross = $amount ;
    } else if ($supId == 39){ //GOLDEN NUTRITIOUS FOOD CORP
        $gross = $amount ;
    } else if ($supId == 40 ) { //NCM
        $gross = $amount ;
    } else if ($supId == 41 ) { //CANDYMAKER
        $gross = $amount ;
    } else if ($supId == 42 ) { //LTHFOOOD
        $gross = $amount ;
    } else if ($supId == 43 ) { //ADVECT
        $gross = $amount / $disc1 / $disc2 ;
    } else if($supId == 44){ //JOSELITO
        $gross = $amount ;
    } else if ($supId == 45 ) { //MAGNOLIA
        $gross = $amount  ;
    } else if ($supId == 46 ) { //PUREFOODS
        $gross = $amount  ;
    } else if ($supId == 47) { //LMB GROCERY net na walay deduction
        $gross = $amount  ; 
    } else if ($supId == 48 ) { //SANMIG
        $gross = $amount  ;
    } else if ($supId == 51 ) { //CANDYMAN
        $gross = $amount  ;
    } else if ($supId == 50 ) { //D' GENESIS
        $gross = $amount  ;
    } else if ($supId == 52 ) { //BISCONFOOD
        $gross = $amount  ;
    } else if ($supId == 53) { //ALLIED EXPRESS
        $gross = $amount   ;
    } else if ($supId == 54) { //G. MARIN
        $gross = $amount   ;
    } else if ($supId == 56) { //COLUMBIA
        $gross = $amount * $vat  ;
    } else if ($supId == 57){ //ASHLAR
        $gross = $amount   ;
    } else if ($supId == 58) { //FPD FOOD
        $gross = $amount  ;
    } else if ($supId == 59) { //ALCL
        $gross = $amount  ;
    } else if ($supId == 60){ //GREEN GOLD
        $gross = $amount  ;
    } else if ($supId == 61){ //PINAKAMASARAP
        $gross = $amount  ;
    } else if ($supId == 62){ //SMART PLASTIC
        $gross = $amount  ;
    } else if ($supId == 63){ //RUFINA
        $gross = $amount  ;
    } else if ($supId == 64){ //JAKA
        $gross = $amount  ;
    }

    return $gross;
}

function backToGross($supId, $amount, $disc1, $disc2, $disc3, $disc4, $disc5,$disc6, $vat)
{
    $gross = 0;
    if ($supId == 1) { //ALASKA
        $gross = ($amount / $disc1 /$disc2) * $vat;
    } else if ($supId == 2) { //js unitrade /7%/4%
        $gross = $amount * $vat;
    } else if ($supId == 5) { //intelligent /10%/10%/4% * VAT        
        $gross =  $amount * $vat  / $disc1 / $disc2 / $disc3; //$amount * 1.12  / 0.90 / 0.90 / 0.96  ; 
    } else if ($supId == 9) { //mondelez 6%or7% * VAT
        $gross = ($amount / $disc1) * $vat;
    } else if ($supId == 16) { //SCPG
        $gross = ($amount / $disc1) * $vat;
    } else if ($supId == 14) { //VALIANT
        $gross = ($amount / $disc1) * $vat;
    } else if ($supId == 13) { //ACS
        $gross = $amount;
    } else if ($supId == 3) { //COSMETIQUE
        $gross = $amount;
    } else if ($supId == 10) { //SUYEN
        $gross = $amount;
    } else if ($supId == 15) { //MCKENZIE
        $gross = ($amount / $disc1) * $vat;
    } else if ($supId == 49) { //ALECO
        $gross = $amount;
    } else if ($supId == 20) { //BIG E
        $gross = $amount;
    } else if ($supId == 4) { //ECO
        $gross = $amount;
    } else if ($supId == 23) { //FOOD INDUSTRIES
        $gross = $amount;
    } else if ($supId == 22) { //FOOD CHOICE
        $gross = $amount / $disc1;
    } else if ($supId == 55) { //GSMI
        $gross = $amount;
    } else if ($supId == 24) { //GREEN CROSS
        $gross = $amount;
    } else if ($supId == 8) { //MEAD JOHNSON
        $gross = ($amount / $disc1) * $vat;
    } else if ($supId == 25){  //KSK        
        $gross = $amount;
    } else if ($supId == 26){  //RECKIT        
        $gross = ($amount / $disc1) * $vat;
    } else if ($supId == 12){  //PEERLESS        
        $gross = $amount ;
    } else if ($supId == 27){ //CLE ACE
        $gross = ($amount / $disc1 / $disc2 / $disc3) * $vat ;
    } else if ($supId == 28){ //KAREILA
        $gross = ($amount / $disc1) * $vat ;
    } else if ($supId == 29){ //RUFINA
        $gross = ($amount / $disc1) * $vat ;
    } else if ($supId == 31){ //KETTLE
        $gross = $amount ;        
    } else if ($supId == 32){ //RPO FINE FOODS
        $gross = $amount ;
    } else if ($supId == 33){ //MARYLAND
        $gross = $amount ;
    } else if ($supId == 34){ //MIRAMAR
        $gross = $amount ;
    } else if ($supId == 35){ //NUTRITIVE
        $gross = $amount ;
    } else if ($supId == 37){ //NATION CONFECTIONERY
        $gross = $amount ;
    } else if ($supId == 38){ //HOWDEE AND YUMEE FOOD CORP
        $gross = $amount ;
    } else if ($supId == 36){ //STATELINE SNACK FOOD CORP
        $gross = $amount ;
    } else if ($supId == 39){ //GOLDEN NUTRITIOUS FOOD CORP
        $gross = $amount ;
    } else if ($supId == 41){ //CANDYMAKER
        $gross = $amount ;
    } else if ($supId == 40){ //NCM
        $gross = $amount ;
    } else if ($supId == 42){ //LTHFOOD
        $gross = $amount ;
    } else if ($supId == 43){ //ADVECT
        $gross = ($amount / $disc1 / $disc2) * $vat ;
    } else if ($supId == 44){ //JOSELITO
        $gross = $amount ;
    } else if ($supId == 45){ //MAGNOLIA
        $gross = $amount ;
    } else if ($supId == 46){ //PUREFOODS
        $gross = $amount ;
    } else if ($supId == 48 ) { //SANMIG
        $gross = $amount  ;
    } else if ($supId == 47 ) { //LMB
        $gross = $amount  ;
    } else if ($supId == 51 ) { //CANDYMAN
        $gross = $amount  ;
    } else if ($supId == 50 ) { //D' GENESIS
        $gross = $amount  ;
    } else if ($supId == 52 ) { //BISCONFOOD
        $gross = $amount  ;
    } else if ($supId == 53) { //ALLIED EXPRESS
        $gross = $amount   ;
    } else if ($supId == 54) { //G. MARIN
        $gross = $amount   ;
    } else if ($supId == 56) { //COLUMBIA
        $gross = ($amount /  $disc1 / $disc2 / $disc3 / $disc4 / $disc5 / $disc6)  * $vat  ;
    } else if ($supId == 57){ //ASHLAR
            $gross = $amount ;        
    } else if ($supId == 58) { //FPD FOOD
        $gross = $amount   ;
    } else if ($supId == 59) { //ALCL
        $gross = $amount   ;
    } else if ($supId == 60){ //GREEN GOLD
        $gross = $amount  ;
    } else if ($supId == 61){ //PINAKAMASARAP
        $gross = $amount  ;
    } else if ($supId == 62){ //SMART PLASTIC
        $gross = $amount  ;
    } else if ($supId == 63){ //RUFINA gross of discount net of vat (GROSSofDiscwoVAT)
        $gross = $amount / $disc1  ; // sa charges ra addan sa vat
    } else if ($supId == 64){ //JAKA
        $gross = $amount  ;
    }

    return $gross;
}

function netPrice($supId, $pricing, $price, $disc1, $disc2, $disc3, $disc4, $disc5,$disc6,  $vat, $cusId = NULL)
{
    $netPrice = 0;
    if ($pricing == "NETofVAT&Disc") {        //mondelez,intelligent,valiant,mead, food choice, mckenzie, cle ace
        if ($supId == 50 ) { //D' GENESIS
            $netPrice  = $price;
        } else if($supId == 53){//ALLIED EXPRES
            $netPrice  = $price;
        } else if ($supId == 54) { //G. MARIN
            $netPrice = $price   ;        
        } else {
            $netPrice  = $price;
        }
        
    } else if ($pricing == "GROSSofVAT&Disc") {
        if ($supId == 3) { //COSMETIQUE
            $netPrice = ($price / $vat) * $disc1 * $disc2 * $disc3;
        } else if ($supId == 10) { //SUYEN
            $netPrice = ($price / $vat) * $disc1;
        } else if ($supId == 13) { //ACS
            $netPrice  = ($price / $vat) * $disc1 * $disc2;
        } else if ($supId == 18) { //ALECO
            $netPrice  = ($price / $vat) * $disc1;
        } else if ($supId == 20) { //BIG E
            $netPrice = ($price / $vat) * $disc1 * $disc2 * $disc3;
        } else if ($supId == 4) { //ECO
            if($cusId == 4 || $cusId == 8 || $cusId == 1){ //if cebu
                $netPrice = ($price / $vat) * $disc1 * $disc2;
            } else {
                $netPrice = ($price / $vat) * $disc1 * $disc2 * $disc3;
            }
            
        } else if ($supId == 23) { //FOOD INDUSTRIES
            $netPrice = ($price / $vat) * $disc1 * $disc2 * $disc3 * $disc4;
        } else if ($supId == 55) { //GSMI
            // $netPrice = ($price / $vat) - $disc1 ;
            $netPrice = ($price - $disc1 - $disc2 - $disc3) / $vat;
        } else if ($supId == 24) { //GREEN CROSS
            $netPrice = ($price / $vat) * $disc1;
        } else if ($supId == 25) { //KSK
            $netPrice = ($price / $vat) * $disc1 * $disc2 * $disc3;
        } else if ($supId == 12){  //PEERLESS        
            $netPrice  = ($price / $vat) * $disc1 * $disc2;
        } else if ($supId == 31){ //KETTLE
            $netPrice  = ($price / $vat) * $disc1 ;
        } else if ($supId == 32){  //RPO FINE        
            $netPrice  = ($price / $vat) * $disc1 ;
        } else if ($supId == 33) { //MARYLAND
            $netPrice  = ($price / $vat) * $disc1 ;
        } else if ($supId == 34){  //MIRAMAR       
            $netPrice  = ($price / $vat) * $disc1 ;
        } else if ($supId == 35){  //NUTRITIVE       
            $netPrice  = ($price / $vat) * $disc1 * $disc2;
        } else if ($supId == 37){  //NATION CONFECTIONERY       
            $netPrice  = ($price / $vat) * $disc1;
        } else if ($supId == 38){  //HOWDEE AND YUMEE FOOD CORP       
            $netPrice  = ($price / $vat) * $disc1 * $disc2;
        } else if ($supId == 36){ //STATELINE SNACK FOOD CORP
            $netPrice  = ($price / $vat) * $disc1 * $disc2;
        } else if ($supId == 39){ //GOLDEN NUTRITIOUS FOOD CORP
            $netPrice  = ($price / $vat) * $disc1 * $disc2;
        } else if ($supId == 41){ //CANDYMAKER
            $netPrice  = ($price / $vat) * $disc1 ;
        } else if ($supId == 40){ //NCM
            $netPrice  = ($price / $vat) * $disc1 * $disc2 * $disc3 * $disc4 * $disc5 ;
        } else if ($supId == 42){ //LTHFOOD
            $netPrice  = ($price / $vat) * $disc1 * $disc2;
        }else if($supId == 44){ //JOSELITO
            $netPrice  = ($price / $vat) * $disc1 ;
        } else if ($supId == 45){ //MAGNOLIA
            $netPrice  = ($price / $vat) * $disc1 * $disc2;
        } else if ($supId == 46){ //PUREFOODS
            $netPrice  = ($price / $vat) * $disc1 * $disc2;
        } else if ($supId == 48){ //SANMIG
            $netPrice  = ($price / $vat) * $disc1 * $disc2;
        } else if ($supId == 51){ //CANDYMAN
            $netPrice  = ($price / $vat) * $disc1 ;
        } else if ($supId == 52 ) { //BISCONFOOD
            $netPrice  = ($price / $vat) * $disc1 * $disc2;
        } else if ($supId == 57) { //ASHLAR
            $netPrice  = ($price / $vat) * $disc1 ;
        } else if ($supId == 58){ //FPD FOOD
            $netPrice  = ($price / $vat) * $disc1 ;
        } else if ($supId == 59){ //ALCL
            $netPrice  = ($price / $vat) * $disc1 ;
        } else if ($supId == 60){ //GREEN GOLD
            $netPrice  = ($price / $vat) * $disc1 ;
        } else if ($supId == 61){ //PINAKAMASARAP
            $netPrice  = ($price / $vat) * $disc1 * $disc2;
        } else if ($supId == 62){ //SMART PLASTIC
            $netPrice  = ($price / $vat) * $disc1 ;
        } else if ($supId == 64){ //JAKA
            $netPrice = ($price - $disc1 - $disc2 ) / $vat;
        } 

    } else if ($pricing == "NETofVATwDisc") {
        if ($supId == 2) { // js unitrade
            $netPrice  = $price * $disc1 * $disc2; //1401.88 * 0.93 * 0.96
        }
    } else if ($pricing == "GROSSofDiscwoVAT") {
        if ($supId == 16) { //SCPG
            $netPrice = $price * $disc1;
        } else if ($supId == 26){  //RECKIT        
            $netPrice = $price * $disc1;
        } else if ($supId == 29) { //RUFINA
            $netPrice = $price * $disc1;
        } else if ($supId == 56){ //COLUMBIA
            $netPrice = $price *  $disc1 * $disc2 *  $disc3 * $disc4 * $disc5 * $disc6;
        } else if ($supId == 63){ //RUFINA
            $netPrice  = $price * $disc1 ;
        }
    } else if ($pricing == "NETofDiscwVAT"){
        if($supId == 1){ //alaska
            $netPrice = $price / $vat;
        } else if ($supId == 28){ //KAREILA
            $netPrice = $price / $vat ;
        } else if ($supId == 43){ //ADVECT
            $netPrice = $price / $vat ;
        }
    }

    return $netPrice;
}

function discountedPrice($supId, $amount, $disc1, $disc2, $disc3, $disc4, $disc5,$disc6, $vat)
{
    $discounted = 0;
    if ($supId == 14) { //valiant
        $discounted = $amount  * $vat; // net of discount & vat, so ibalik ra ang vat aron makuha ang discounted price
    } else if ($supId == 15) { //MCKENZIE
        $discounted = $amount * $vat;
    } else if ($supId == 3) { //COSMETIQUE
        $discounted = $amount * $disc1 * $disc2 * $disc3;
    } else if ($supId == 13) { //ACS
        $discounted = $amount * $disc1 * $disc2;
    } else if ($supId == 5) { //INTELLIGENT
        $discounted = $amount * $vat;
    } else if ($supId == 2) { //JS
        $discounted = $amount * $vat;
    } else if ($supId == 10) { //SUYEN 
        $discounted = $amount * $disc1;
    } else  if ($supId == 16) { //SCPG
        $discounted = $amount * $vat;
    } else  if ($supId == 9) { //MONDELEZ
        $discounted = $amount * $disc2 * $disc3 * $vat;
    } else  if ($supId == 49) { //ALECO
        $discounted = $amount * $disc1;
    } else if ($supId == 20) { //BIG E
        $discounted = $amount * $disc1;
    } else if ($supId == 4) { //ECO
        $discounted = $amount * $disc1 * $disc2 * $disc3;
    } else if ($supId == 23) { //FOOD INDUSTRIES
        $discounted = $amount * $disc1 * $disc2 * $disc3 * $disc4;
    } else if ($supId == 22) { //FOOD CHOICE
        $discounted = $amount * $disc1;
    } else if ($supId == 55) { //GSMI
        $discounted = $amount - $disc1 - $disc2 - $disc3;
    } else if ($supId == 24) { //GREEN CROSS
        $discounted = $amount * $disc1;
    } else if ($supId == 8) { //MEAD
        $discounted = $amount * $vat; //discounted price ang PSI price, ibalik ra ang vat
    } else if ($supId == 25) { //KSK
        $discounted = $amount * $disc1 * $disc2 * $disc3 ;
    } else if ($supId == 1) { //ALASKA
        $discounted = $amount * $vat  ;
    } else if ($supId == 26) { //RECKIT
        $discounted = $amount * $vat ;
    } else if ($supId == 12) { //PEERLESS
        $discounted = $amount * $disc1 * $disc2  ;
    } else if ($supId == 27) { //CLE ACE
        $discounted = $amount * $vat ;
    } else if ($supId == 28) { //KAREILA
        $discounted = $amount * $vat ;
    } else if ($supId == 31) { //KETTLE
        $discounted = $amount * $disc1 ;
    } else if ($supId == 32) { //RPO FINE
        $discounted = $amount * $disc1 ;
    } else if ($supId == 33) { //MARYLAND
        $discounted = $amount * $disc1 ;
    } else if ($supId == 34) { //MIRAMAR
        $discounted = $amount * $disc1 ;
    } else if ($supId == 35) { //MIRAMAR
        $discounted = $amount * $disc1 * $disc2  ;
    } else if ($supId == 37) { //NATION CONFECTIONERY
        $discounted = $amount * $disc1  ;   
    } else if ($supId == 38) { //HOWDEE AND YUMEE FOOD CORP
        $discounted = $amount * $disc1 * $disc2  ;
    } else if ($supId == 36) { //STATELINE SNACK FOOD CORP
        $discounted = $amount * $disc1 * $disc2  ;
    } else if ($supId == 39) { //GOLDEN NUTRITIOUS FOOD CORP
        $discounted = $amount * $disc1 * $disc2  ;
    } else if ($supId == 41) { //CANDYMAKER
        $discounted = $amount * $disc1   ;
    } else if ($supId == 40) { //NCM
        $discounted = $amount * $disc1 * $disc2 * $disc3 * $disc4 * $disc5  ;
    } else if ($supId == 42) { //LTHFOOD
        $discounted = $amount * $disc1 * $disc2  ;
    } else if ($supId == 43) { //ADVECT
        $discounted = $amount * $vat  ;
    } else if($supId == 44){ //JOSELITO
        $discounted = $amount * $disc1   ;
    } else if ($supId == 45) { //MAGNOLIA
        $discounted = $amount * $disc1 * $disc2  ;
    } else if ($supId == 46) { //PUREFOODS
        $discounted = $amount * $disc1 * $disc2  ;
    } else if ($supId == 48) { //SANMIG
        $discounted = $amount * $disc1 * $disc2  ;
    } else if ($supId == 47) { //LMB is net na walay deductions so return the amount only
        $discounted = $amount  ;
    } else if ($supId == 51) { //CANDYMAN
        $discounted = $amount * $disc1   ;
    } else if ($supId == 50) { //D' GENESIS
        $discounted = $amount  ;
    } else if ($supId == 52) { //BISCONFOOD
        $discounted = $amount * $disc1 * $disc2    ;
    } else if ($supId == 53) { //ALLIED EXPRESS
        $discounted = $amount   ;
    } else if ($supId == 54) { //G. MARIN
        $discounted = $amount   ;
    } else if ($supId == 56) { //COLUMBIA
        $discounted = $amount * $vat   ;
    } else if ($supId == 57){ //ASHLAR
        $discounted = $amount * $disc1   ;
    } else if ($supId == 58){ //FPD FOOD
        $discounted  = $amount * $disc1 ;
    } else if ($supId == 59){ //ALCL
        $discounted  = $amount * $disc1 ;
    } else if ($supId == 60){ //GREEN GOLD
        $discounted  = $amount * $disc1 ;
    } else if ($supId == 61){ //PINAKAMASARAP
        $discounted  = $amount * $disc1 ;
    } else if ($supId == 62){ //SMART
        $discounted  = $amount * $disc1 ;
    } else if ($supId == 62){ //SMART
        $discounted  = $amount * $disc1 ;
    } else if ($supId == 63){ //RUFINA
        $discounted  = $amount * $disc1 ;
    } else if ($supId == 64) { //JAKA
        $discounted = $amount - $disc1 - $disc2 ;
    }

    return $discounted;
}

function netPricePi($supId, $price, $disc1, $disc2, $disc3, $disc4, $disc5,$disc6, $vat)
{
    $netPrice = 0;
    if ($supId == 3) { //COSMETIQUE
        $netPrice = ($price / $vat) * $disc1 * $disc2 * $disc3;
    } else if ($supId == 10) { //SUYEN
        $netPrice = ($price / $vat) * $disc1;
    } else if ($supId == 13) { //ACS
        $netPrice  = ($price / $vat) * $disc1 * $disc2;
    } else if ($supId == 2) { // js unitrade
        $netPrice  = ($price * $disc1 * $disc2) / $vat; //1401.88 * 0.93 * 0.96
    } else if ($supId == 16) { //SCPG
        $netPrice = ($price / $vat) * $disc1;
    } else if ($supId == 15) { //MCKENZIE
        $netPrice =  ($price / $vat)  * $disc1  ;
    } else if ($supId == 5) { //INTELLIGENT
        $netPrice = ($price / $vat)  * $disc1 * $disc2 * $disc3; //($price / 1.12)  * 0.90 * 0.90 * 0.96 ;
    } else if ($supId == 14) { //VALIANT
        $netPrice = ($price / $vat) * $disc1;
    } else if ($supId == 49) { //ALECO 
        $netPrice = ($price / $vat) * $disc1;
    } else if ($supId == 9) { //MONDELEZ 
        $netPrice = ($price / $vat) * $disc1;
    } else if ($supId == 20) { //BIG E
        $netPrice = ($price / $vat) * $disc1 * $disc2 * $disc3;
    } else if ($supId == 4) { //ECO
        $netPrice = ($price / $vat) * $disc1 * $disc2 * $disc3;
    } else if ($supId == 23) { //FOOD INDUSTRIES
        $netPrice = ($price / $vat) * $disc1 * $disc2 * $disc3 * $disc4;
    } else if ($supId == 22) { //FOOD CHOICE
        $netPrice = ($price / $vat) * $disc1;
    } else if ($supId == 55) { //GSMI
        $netPrice = ($price -  $disc1 - $disc2 - $disc3) / $vat;
    } else if ($supId == 24) { //GREEN CROSS
        $netPrice = ($price / $vat) * $disc1;
    } else if ($supId == 8) { //MEAD JOHNSON
        $netPrice = ($price / $vat) * $disc1;
    } else if ($supId == 25) { //KSK
        $netPrice = ($price / $vat) * $disc1 * $disc2 * $disc3;
    } else if ($supId == 1) { //ALASKA
        $netPrice = ($price / $vat) * $disc1 * $disc2 ;
    } else if ($supId == 26){ //RECKIT
        $netPrice = ($price / $vat) * $disc1 ;
    } else if ($supId == 12) { //PEERLESS
        $netPrice  = ($price / $vat) * $disc1 * $disc2;
    } else if ($supId == 27) { //CLE ACE
        $netPrice  = ($price / $vat) * $disc1 * $disc2 * $disc3;
    } else if ($supId == 28) { //KAREILA
        $netPrice  = ($price / $vat) * $disc1 ;
    } else if ($supId == 31) { //KETTLE
        $netPrice  = ($price / $vat) * $disc1 ;
    } else if ($supId == 32) { //RPO
        $netPrice  = ($price / $vat) * $disc1 ;
    } else if ($supId == 33) { //MARYLAND
        $netPrice  = ($price / $vat) * $disc1 ;
    } else if ($supId == 34) { //MIRAMAR
        $netPrice  = ($price / $vat) * $disc1 ;
    } else if ($supId == 35) { //NUTRITIVE
        $netPrice  = ($price / $vat) * $disc1 * $disc2;
    } else if ($supId == 36) { //STATELINE SNACK FOOD CORP
        $netPrice  = ($price / $vat) * $disc1 * $disc2;
    } else if ($supId == 37) { //NATION CONFECTIONERY
        $netPrice  = ($price / $vat) * $disc1;
    } else if ($supId == 38) { //HOWDEE AND YUMEE FOOD CORP
        $netPrice  = ($price / $vat) * $disc1 * $disc2;
    } else if ($supId == 39) { //GOLDEN NUTRITIOUS FOOD CORP
        $netPrice  = ($price / $vat) * $disc1 * $disc2;
    } else if ($supId == 40) { //NCM
        $netPrice  = ($price / $vat) * $disc1 * $disc2 * $disc3 * $disc4 * $disc5;
    } else if ($supId == 41) { //THE CANDYMAKER
        $netPrice  = ($price / $vat) * $disc1 ;
    } else if ($supId == 42) { //LTHFOOD
        $netPrice  = ($price / $vat) * $disc1 * $disc2;
    } else if ($supId == 43) { //ADVECT
        $netPrice  = ($price / $vat) * $disc1 * $disc2;
    } else if ($supId == 44){ //JOSELITO
        $netPrice  = ($price / $vat) * $disc1 ;
    } else if ($supId == 45) { //MAGNOLIA
        $netPrice  = ($price / $vat) * $disc1 * $disc2;
    } else if ($supId == 46) { //PUREFOODS
        $netPrice  = ($price / $vat) * $disc1 * $disc2;
    } else if ($supId == 48) { //SANMIG
        $netPrice  = ($price / $vat) * $disc1 * $disc2;
    } else if ($supId == 47) { //LMB
        $netPrice  = $price ;
    } else if ($supId == 51) { //CANDYMAN
        $netPrice  = ($price / $vat) * $disc1 ;
    } else if ($supId == 50) { //D' GENESIS
        $netPrice  = $price ;
    } else if ($supId == 52) { //BISCONFOOD
        $netPrice  = ($price / $vat) * $disc1 * $disc2;
    } else if ($supId == 53) { //ALLIED EXPRESS
        $netPrice  = $price ;
    } else if ($supId == 54) { //G. MARIN
        $netPrice  = $price ;
    } else if ($supId == 56) { //COLUMBIA
        $netPrice  = ($price / $vat) * $disc1 * $disc2 * $disc3 * $disc4 * $disc5 * $disc6;
    } else if ($supId == 57){ //ASHLAR
        $netPrice  = ($price / $vat) * $disc1 ;
    } else if ($supId == 58) { //FPD FOOD
        $netPrice  = ($price / $vat) * $disc1 ;
    } else if ($supId == 59) { //ALCL
        $netPrice  = ($price / $vat) * $disc1 ;
    } else if ($supId == 60) { //GREEN GOLD
        $netPrice  = ($price / $vat) * $disc1 ;
    } else if ($supId == 61) { //PINAKAMASARAP
        $netPrice  = ($price / $vat) * $disc1 ;
    } else if ($supId == 62) { //SMART
        $netPrice  = ($price / $vat) * $disc1 ;
    } else if ($supId == 63) { //RUFINA
        $netPrice  = ($price / $vat) * $disc1 ;
    } else if ($supId == 64) { //JAKA
        $netPrice = ($price -  $disc1 - $disc2) / $vat;
    }

    return $netPrice;
}

function discountedPricePi($supId, $amount, $disc1, $disc2, $disc3, $disc4, $disc5,$disc6)
{
    $discounted = 0;
    if ($supId == 3) { //COSMETIQUE
        $discounted = $amount * $disc1 * $disc2 * $disc3;
    } else if ($supId == 15) { //MCKENZIE
        $discounted = $amount * $disc1;
    } else if ($supId == 13) { //ACS
        $discounted = $amount * $disc1 * $disc2;
    } else if ($supId == 5) { //INTELLIGENT
        $discounted = $amount * $disc1 * $disc2 * $disc3; //$amount * 0.90 * 0.90 * 0.96  ; 
    } else if ($supId == 2) { //JS
        $discounted = $amount * $disc1 * $disc2;
    } else if ($supId == 14) { //VALIANT 
        $discounted = $amount * $disc1;
    } else if ($supId == 10) { //SUYEN 
        $discounted = $amount * $disc1;
    } else if ($supId == 16) { //SCPG
        $discounted = $amount  * $disc1;
    } else if ($supId == 9) { //MONDELEZ
        // $discounted = $amount  * $disc1;
        $discounted = $amount  * $disc1 * $disc2 * $disc3;
    } else if ($supId == 49) { //ALECO
        $discounted = $amount  * $disc1;
    } else if ($supId == 20) { //BIG E
        $discounted = $amount * $disc1;
    } else if ($supId == 4) { //ECO
        $discounted = $amount * $disc1 * $disc2 * $disc3;
    } else if ($supId == 23) { //FOOD INDUSTRIES
        $discounted = $amount * $disc1 * $disc2 * $disc3 * $disc4;
    } else if ($supId == 22) { //FOOD CHOICE
        $discounted = $amount * $disc1;
    } else if ($supId == 55) { //GSMI
        $discounted = $amount - $disc1 - $disc2 - $disc3;
    } else if ($supId == 24) { //GREEN CROSS
        $discounted = $amount * $disc1;
    } else if ($supId == 8) { //MEAD JOHNSON
        $discounted = $amount * $disc1;
    } else if ($supId == 25) { //KSK
        $discounted = $amount * $disc1 * $disc2 * $disc3;
    } else if ($supId == 1) { //ALASKA
        $discounted = $amount * $disc1 * $disc2 ;
    } else if ($supId == 26) { //RECKIT
        $discounted = $amount * $disc1  ;
    } else if ($supId == 12) { //PEERLESS
        $discounted = $amount * $disc1 * $disc2 ;
    } else if ($supId == 27) { //CLE ACE
        $discounted = $amount * $disc1 * $disc2 * $disc3;
    } else if ($supId == 28) { //KAREILA
        $discounted = $amount * $disc1;
    } else if ($supId == 31) { //KETTLE
        $discounted = $amount * $disc1;
    } else if ($supId == 32) { //RPO
        $discounted = $amount * $disc1;
    } else if ($supId == 33) { //MARYLAND
        $discounted = $amount * $disc1;
    } else if ($supId == 34) { //MIRAMAR
        $discounted = $amount * $disc1;
    } else if ($supId == 35) { //NUTRITIVE
        $discounted = $amount * $disc1 *  $disc2 ;
    } else if ($supId == 36) { //STATELINE SNACK FOOD CORP
        $discounted = $amount * $disc1 *  $disc2  ;
    } else if ($supId == 37) { //NATION CONFECTIONERY
        $discounted = $amount * $disc1  ;
    } else if ($supId == 38) { //HOWDEE AND YUMEE FOOD CORP
        $discounted = $amount * $disc1 *  $disc2  ;
    } else if ($supId == 39) { //GOLDEN NUTRITIOUS FOOD CORP
        $discounted = $amount * $disc1 *  $disc2  ;
    } else if ($supId == 40){ //NCM
        $discounted = $amount * $disc1 * $disc2 * $disc3 * $disc4 * $disc5  ;
    } else if ($supId == 41) { //CANDYMAKER
        $discounted = $amount * $disc1   ;
    } else if ($supId == 42) { //LTHFOOD
        $discounted = $amount * $disc1 * $disc2   ;
    } else if ($supId == 43) { //ADVECT
        $discounted = $amount * $disc1 * $disc2   ;
    } else if ($supId == 44){ //JOSELITO
        $discounted = $amount * $disc1   ;
    } else if ($supId == 45) { //MAGNOLIA
        $discounted = $amount * $disc1 * $disc2   ;
    } else if ($supId == 46) { //PUREFOODS
        $discounted = $amount * $disc1 * $disc2   ;
    } else if ($supId == 48) { //SANMIG
        $discounted = $amount * $disc1 * $disc2   ;
    } else if ($supId == 47) { //LMB
        $discounted = $amount   ;
    } else if ($supId == 51){ //JOSELITO
        $discounted = $amount * $disc1   ;
    } else if ($supId == 50) { //D' GENESIS
        $discounted = $amount   ;
    } else if ($supId == 52) { //BISCONFOOD
        $discounted = $amount * $disc1 * $disc2   ;
    } else if ($supId == 53) { //ALLIED EXPRESS
        $discounted = $amount   ;
    } else if ($supId == 54) { //G. MARIN
        $discounted = $amount   ;
    } else if ($supId == 56) { //COLUMBIA
        $discounted = $amount * $disc1 * $disc2 * $disc3 * $disc4 * $disc5  * $disc6  ;
    } else if ($supId == 57){ //ASHLAR
        $discounted = $amount * $disc1  ;
    } else if ($supId == 58) { //FPD FOOD
        $discounted = $amount * $disc1  ;
    } else if ($supId == 59) { //ALCL
        $discounted = $amount * $disc1  ;
    } else if ($supId == 60) { //GREEN GOLD
        $discounted = $amount * $disc1  ;
    } else if ($supId == 61) { //PINAKAMASARAP
        $discounted = $amount * $disc1  ;
    } else if ($supId == 62) { //SMART PLASTIC
        $discounted = $amount * $disc1  ;
    } else if ($supId == 63) { //RUFINA
        $discounted = $amount * $disc1  ;
    } else if ($supId == 64) { //JAKA
        $discounted = $amount - $disc1 - $disc2 ;
    }

    return $discounted;
}

/* search by value multid */
function unique_multidim_array($array, $key)
{
    $temp_array = array();
    $i = 0;
    $key_array = array();

    foreach ($array as $val) {
        if (!in_array($val[$key], $key_array)) {
            $key_array[$i] = $val[$key];
            $temp_array[$i] = $val;
        }
        $i++;
    }
    return $temp_array;
}


function array_flatten($array)
{

    $return = array();
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $return = array_merge($return, array_flatten($value));
        } else {
            $return[$key] = $value;
        }
    }
    return $return;
}


function checkNoItemCodes($array)
{
    $noSetup = array_filter($array, function($value){
                    if( !$value['id'] || !$value['item_division'] || !$value['item_department_code'] || !$value['item_group_code'] ){
                        return $value ;
                    }
                });

    return $noSetup ;
}


function arraycount($array, $value){
    $counter = 0;
    foreach($array as $thisvalue) /*go through every value in the array*/
        {
            if($thisvalue === $value){ /*if this one value of the array is equal to the value we are checking*/
            $counter++; /*increase the count by 1*/
            }
        }
        return $counter;
}


