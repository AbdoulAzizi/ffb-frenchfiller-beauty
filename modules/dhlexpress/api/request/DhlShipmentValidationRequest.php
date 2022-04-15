<?php
/**
 * 2007-2021 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2021 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class DhlShipmentValidationRequest
 */
class DhlShipmentValidationRequest extends AbstractDhlRequest
{
    /**
     *
     */
    const METHOD = 'POST';
    /**
     *
     */
    const XML_TEMPLATE = '/xml/ShipmentValidation.xml';

    /**
     * @return string
     */
    public function getMethod()
    {
        return self::METHOD;
    }

    /**
     * @return string
     */
    public function getXMLTemplateFile()
    {
        return self::XML_TEMPLATE;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->xml->asXML();
    }

    /**
     * @return string
     */
    public function getXmlName()
    {
        return false;
    }

    /**
     * @param SimpleXMLExtended $response
     * @return DhlShipmentValidationResponse
     */
    public function buildResponse(SimpleXMLExtended $response)
    {
        if (!$response) {
            die();
        }
        $shipmentValidationRequest = DhlShipmentValidationResponse::buildFromResponse($response);

        return $shipmentValidationRequest;
    }
    
    /**
     * @param string $version
     */
    public function setMetaDataVersion($version)
    {
        $this->xml->Request->MetaData->SoftwareVersion = $version;
    }

    /**
     * @param string $languageCode
     */
    public function setLanguageCode($languageCode)
    {
        $this->xml->LanguageCode = $languageCode;
    }

    /**
     * @param array $billingDetails
     */
    public function setBilling($billingDetails)
    {
        foreach ($billingDetails as $billingDetailKey => $billingDetailValue) {
            $this->xml->Billing->$billingDetailKey = $billingDetailValue;
        }
    }

    /**
     * @param array $consigneeDetails
     */
    public function setConsignee($consigneeDetails)
    {
        foreach ($consigneeDetails as $consigneeDetailKey => $consigneeDetailValue) {
            if ($consigneeDetailKey != 'AddressLine') {
                if ($consigneeDetailValue) {
                    $this->xml->Consignee->$consigneeDetailKey = $consigneeDetailValue;
                }
            } else {
                $this->xml->Consignee->AddressLine = $consigneeDetailValue[0];
                if (isset($consigneeDetailValue[1])) {
                    /** @var SimpleXMLExtended $addressLine2 */
                    $addressLine2 = $this->xml->Consignee->AddressLine->insertAfter(
                        new SimpleXMLExtended('<AddressLine><![CDATA['.$consigneeDetailValue[1].']]></AddressLine>')
                    );
                }
                if (isset($consigneeDetailValue[2]) && isset($addressLine2)) {
                    $addressLine2->insertAfter(
                        new SimpleXMLExtended('<AddressLine><![CDATA['.$consigneeDetailValue[2].']]></AddressLine>')
                    );
                }
            }
        }
    }

    /**
     * @param array $contactDetails
     */
    public function setConsigneeContact($contactDetails)
    {
        foreach ($contactDetails as $contactDetailKey => $contactDetailValue) {
            $this->xml->Consignee->Contact->$contactDetailKey = $contactDetailValue;
        }
    }

    /**
     * @param array $shipmentDetails
     */
    public function setShipmentDetails($shipmentDetails)
    {
        foreach ($shipmentDetails as $shipmentDetailKey => $shipmentDetailValue) {
            if ($shipmentDetailKey != 'Pieces' && $shipmentDetailKey != 'LocalProductCode') {
                $this->xml->ShipmentDetails->$shipmentDetailKey = $shipmentDetailValue;
            }
        }
        if (isset($shipmentDetails['LocalProductCode']) && $shipmentDetails['LocalProductCode']) {
            $this->xml->ShipmentDetails->GlobalProductCode->insertAfter(
                new SimpleXMLExtended('<LocalProductCode>'.$shipmentDetails['LocalProductCode'].'</LocalProductCode>')
            );
        }
        if (isset($shipmentDetails['Pieces']) && is_array($shipmentDetails['Pieces'])) {
            foreach ($shipmentDetails['Pieces'] as $pieces) {
                $pieceElem = $this->xml->ShipmentDetails->Pieces->addChild('Piece');
                foreach ($pieces as $pieceKey => $piece) {
                    $pieceElem->addChild($pieceKey, $piece);
                }
            }
        }
    }

    /**
     * @param array $shipperDetails
     */
    public function setShipper($shipperDetails)
    {
        foreach ($shipperDetails as $shipperDetailKey => $shipperDetailValue) {
            if ($shipperDetailKey != 'AddressLine') {
                $this->xml->Shipper->$shipperDetailKey = $shipperDetailValue;
            } else {
                if (is_array($shipperDetailValue)) {
                    $this->xml->Shipper->AddressLine = $shipperDetailValue[0];
                    if (isset($shipperDetailValue[1])) {
                        /** @var SimpleXMLExtended $addressLine2 */
                        $addressLine2 = $this->xml->Shipper->AddressLine->insertAfter(
                            new SimpleXMLExtended('<AddressLine><![CDATA['.$shipperDetailValue[1].']]></AddressLine>')
                        );
                    }
                    if (isset($shipperDetailValue[2]) && isset($addressLine2)) {
                        $addressLine2->insertAfter(
                            new SimpleXMLExtended('<AddressLine><![CDATA['.$shipperDetailValue[1].']]></AddressLine>')
                        );
                    }
                }
            }
        }
    }

    /**
     * @param array $contactDetails
     */
    public function setContactShipper($contactDetails)
    {
        foreach ($contactDetails as $contactDetailKey => $contactDetailValue) {
            $this->xml->Shipper->Contact->$contactDetailKey = $contactDetailValue;
        }
    }

    /**
     * @param array $extracharges
     */
    public function setSpecialService($extracharges)
    {
        foreach ($extracharges as $extracharge) {
            $this->xml->Shipper->insertAfter(
                new SimpleXMLExtended(
                    '<SpecialService><SpecialServiceType>'.$extracharge.'</SpecialServiceType></SpecialService>'
                )
            );
        }
    }
    
    /**
     * @param array $dgCodes
     * @param string $codeUN
     */
    public function setDangerousCode($dgCodes, $codeUN)
    {
         $this->xml->Label->insertAfter(
            new SimpleXMLExtended('<DGs></DGs>')
        );
        foreach ($dgCodes as $code) {
            if($code == 'E01'){
                $dg = $this->xml->DGs->addChild('DG');
                $dg->addChild('DG_ContentID', $code);
                $dg->addChild('DG_UNCode', $codeUN);
            }else{
                $dg = $this->xml->DGs->addChild('DG');
                $dg->addChild('DG_ContentID', $code);
            }
        }
    }  
    
    /**
     * @param array $insuredValue
     */
    public function setInsuredValue($insuredValue)
    {
        $this->xml->ShipmentDetails->DimensionUnit->insertAfter(
            new SimpleXMLExtended('<InsuredAmount>'.$insuredValue.'</InsuredAmount>')
        );
    }

    /**
     * @param array $duty
     */
    public function setDuty($duty)
    {
        $this->xml->Consignee->insertAfter(
            new SimpleXMLExtended('<Dutiable></Dutiable>')
        );
        $this->xml->Dutiable->addChild('DeclaredValue', $duty['DeclaredValue']);
        $this->xml->Dutiable->addChild('DeclaredCurrency', $duty['DeclaredCurrency']);
    }

    public function setDutyActivated($duty)
    {
        $this->xml->Consignee->insertAfter(
            new SimpleXMLExtended('<Dutiable></Dutiable>')
        );
        $this->xml->Dutiable->addChild('DeclaredValue', $duty['DeclaredValue']);
        $this->xml->Dutiable->addChild('DeclaredCurrency', $duty['DeclaredCurrency']);
        $this->xml->Dutiable->addChild('TermsOfTrade', $duty['TermsOfTrade']);
    }
    
    /**
     * @param string $isDutiableValue
     */
    public function setIsDutiable($isDutiableValue)
    {
        $this->xml->ShipmentDetails->IsDutiable = $isDutiableValue;
    }

    /**
     * @param array $label
     */
    public function setLabelImageFormat($label)
    {
        $this->xml->LabelImageFormat = $label['LabelImageFormat'];
        $this->xml->Label->LabelTemplate = $label['LabelTemplate'];
    }

    /**
     * @param string $flag
     */
    public function setRequestArchiveDoc($flag)
    {
        $this->xml->RequestArchiveDoc = $flag;
    }

    /**
     * @param string $referenceID
     */
    public function setReferenceID($referenceID)
    {
        $this->xml->Reference->ReferenceID = $referenceID;
    }

    /**
     * @param string $labelRegText
     */
    public function setLabelRegText($labelRegText)
    {
        $this->xml->LabelRegText = $labelRegText;
    }
}
