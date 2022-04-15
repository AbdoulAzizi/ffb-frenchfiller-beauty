<?php
/**
 * MODULE PRESTASHOP OFFICIEL CHRONOPOST
 *
 * LICENSE : All rights reserved - COPY AND REDISTRIBUTION FORBIDDEN WITHOUT PRIOR CONSENT FROM OXILEO
 * LICENCE : Tous droits réservés, le droit d'auteur s'applique - COPIE ET REDISTRIBUTION INTERDITES
 * SANS ACCORD EXPRES D'OXILEO
 *
 * @author    Oxileo SAS <contact@oxileo.eu>
 * @copyright 2001-2018 Oxileo SAS
 * @license   Proprietary - no redistribution without authorization
 */

header('Content-type: text/plain');
require('../../../config/config.inc.php');

require_once('../libraries/PDFMerger.php');
@$pdf = new PDFMerger;
$pdfs = $_POST["pdfs"];

foreach ($pdfs as $pdf_path){
    $realpath = "../".$pdf_path;
    if (file_exists($realpath)) {
        @$pdf->addPDF($realpath, 'all');
    }
}

$name = '/skybills/Chronopost-LT-'.date('Ymd-Hi').'.pdf';
$pdf->merge('file', '..'.$name);

echo $name;

