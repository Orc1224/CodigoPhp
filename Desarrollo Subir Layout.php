<?php
set_time_limit(600);
$dataCSV;
$lnuError = 0;
$mjs = '';
$max = false;
$filename = substr($_POST['popup_file'],strrpos($_POST['popup_file'],"\\")+1);
$fileExt = new SplFileInfo($filename);
$pstFileExtension = $fileExt->getExtension();
if($pstFileExtension == 'csv'){
    $dataCSV = file_get_contents("upload\\".$_POST['popup_file_name']);
    $lines = explode(PHP_EOL, $dataCSV);
    if(!empty($lines)){
        $base = getSqlSystemVars("insert");
        foreach ($base as $insert) {
            $values = str_replace("\\","'",$insert);
            $values = str_replace("'","",$insert);
            $values = explode(",",$values);
        }
        $lstCodeLoad = getFromSQL("SELECT ISNULL(MAX(Code),0)+1 AS Code FROM prj_promotions_head", 0, 0);
        for ($i=0; $i < count($lines); $i++) {
            $insertValues = explode(",", $lines[$i]);
            $insertValues[0] = strtoupper(str_replace(' ','',$insertValues[0]));
            if ($insertValues[0] == 'DETALLE') {
                $table = 'prj_promotions_head';
                $array = [
                    'Platform' => $values[4],
                    'Create_Date' => "".$values[0],
                    'Create_User' => $values[1],
                    'Modify_Date' => "".$values[2],
                    'Modify_User' => $values[3],
                    'Code' => $lstCodeLoad,
                    'Code_Type_Apply' => "".$insertValues[1] ?? NULL,
                    'Code_ofClient' => "".$insertValues[2] ?? NULL,
                    // 'Head_Line' => $insertValues[3] ?? NULL, 
                    'Apply_Gift' => $insertValues[3] ?? NULL,
                    'Apply_Val' => $insertValues[4] ?? NULL,
                    'Head_Table' => $insertValues[5] ?? NULL,
                    'Description' => "".$insertValues[6] ?? NULL,
                    'Apply_to' => $insertValues[7] ?? NULL,
                    'Type' => $insertValues[8] ?? NULL,
                    'Quantity_from' => $insertValues[9] ?? NULL,
                    'Per_Client' => $insertValues[10] ?? NULL,
                    'Modificable' => 0,
                    'Activable' => 1,
                    'Quantity_Ini' => $insertValues[11] ?? NULL,
                    'Quantity_Fin' => $insertValues[12] ?? NULL,
                ];
                $resultUpdate = insertToDB($table, $array);
                $max = ($resultUpdate == 1) ? true : false;
                $flag = ($max != false) ? true : false;
            } elseif ($insertValues[0] == 'ORG' && $max == true) {
                $table = 'prj_promotions_organization';
                $array = [
                    'Platform' => $values[4],
                    'Create_Date' => "".$values[0],
                    'Create_User' => $values[1],
                    'Modify_Date' => "".$values[2],
                    'Modify_User' => $values[3],
                    'Code_Promotion' => $lstCodeLoad,
                    'Code_Unit_Org' => $insertValues[1],
                    'Code_Sales_Org' => $insertValues[2]
                ];
                $resultUpdate = insertToDB($table, $array);
                $max = ($resultUpdate == 1) ? true : false ;
            } elseif ($insertValues[0] == 'EVALUAR' && $max == true) {
                $table = 'prj_promotions_hierarchy';
                $array = [
                    'Platform' => $values[4],
                    'Create_Date' => "".$values[0],
                    'Create_User' => $values[1],
                    'Modify_Date' => "".$values[2],
                    'Modify_User' => $values[3],
                    'Code_Promotion' => $lstCodeLoad,
                    'Code_Entity' => $insertValues[1]
                ];
                $resultUpdate = insertToDB($table, $array);
                $max = ($resultUpdate == 1) ? true : false ;
            } elseif ($insertValues[0] == 'REGALAR' && $max == true) {
                $table = 'prj_promotions_hier_gift';
                $array = [
                    'Platform' => $values[4],
                    'Create_Date' => "".$values[0],
                    'Create_User' => $values[1],
                    'Modify_Date' => "".$values[2],
                    'Modify_User' => $values[3],
                    'Code_Promotion' => $lstCodeLoad,
                    'Code_Entity' => $insertValues[1],
                    // 'Quantity_Gift' => $insertValues[2],
                    // 'Maximun_Gift' => $insertValues[3],
                    // 'Price_Gift' => $insertValues[4],
                    // 'Unit_Sales' => $insertValues[5],
                    // 'Code_Currency' => $insertValues[6],
                ];
                $resultUpdate = insertToDB($table, $array);
                $max = ($resultUpdate == 1) ? true : false ;
            } elseif ($insertValues[0] == 'VENDEDORES') {
                $table = 'prj_promotions_sellers';
                $guid_prom = getFromSQL("SELECT ISNULL(MAX(Guid_Prom),0)+1 AS Code FROM prj_promotions_sellers", 0, 0);
                $array = [
                    'Platform' => $values[4],
                    'Create_Date' => "".$values[0],
                    'Create_User' => $values[1],
                    'Modify_Date' => "".$values[2],
                    'Modify_User' => $values[3],
                    'Code_Promotion' => $lstCodeLoad,
                    'Code_Seller' => $insertValues[1],
                    'Guid_Prom' => $guid_prom,
                    'Modificable' => 0,
                    'Activable' => 1,
                    'Quantity_Ini' => $insertValues[2],
                    'Quantity_Fin' => $insertValues[3],
                ];
                $resultUpdate = insertToDB($table, $array);
                $max = ($resultUpdate == 1) ? true : false ;
            } elseif ($insertValues[0] == 'USUARIOS') {
                $table = 'prj_promotions_type_user';
                $array = [
                    'Platform' => $values[4],
                    'Create_Date' => "".$values[0],
                    'Create_User' => $values[1],
                    'Modify_Date' => "".$values[2],
                    'Modify_User' => $values[3],
                    'Code_Promotion' => $lstCodeLoad,
                    'Code_Type' => $insertValues[1],
                ];
                $resultUpdate = insertToDB($table, $array);
                // if($resultUpdate == 1){
                //     $lstCodeLoad++;
                //     $max = true;
                // }
            }
            writeCustomLog("Flag: ".$flag);
            writeCustomLog("Code: ".$lstCodeLoad);
            $lstCodeLoad = ($flag != false) ? $lstCodeLoad++ : $lstCodeLoad;
            writeCustomLog("Flag Incremento: ".$flag);
            writeCustomLog("Code Incremento: ".$lstCodeLoad);
            if($max == false){
                $mjs .= "Ocurrio un error al guardar la informacion";
                writeCustomLog("Table: ".$table);
                $lnuError ++;
                break;
            }
        }
    } else {
        /* Error al Leer el Archivo */
        $mjs .= "Error al leer el archivo";
        $lnuError++;
    }
} else {
    /* Archivo no Valido */
    $mjs .= "El archivo no es de una extencion valida CSV.";
    $lnuError++;
}

if($lnuError > 0){
    showMessage('<div><img src="images/semwarning.gif" style="width: 16px;height: 16px;">' .' '. $mjs . '</div>');
}

function insertToDB($tableName, $array){
    $columns = implode(", ", array_keys($array));
    $values = implode(", ", array_map(function($value){
        return "'".addslashes($value)."'";
    }, array_values($array)));
    $sql = "INSERT INTO $tableName ($columns) VALUES ($values);";
    $queryResult = updateSQL($sql);
    return $queryResult;
}