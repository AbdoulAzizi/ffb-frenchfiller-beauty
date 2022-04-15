<?php

class EntityTypeMapper
{
    public static function getEntityTypeNameByAlias($alias)
    {
        $entityTypesAndAliases = self::entityTypes();

        if (array_key_exists($alias, $entityTypesAndAliases)) {
            return $entityTypesAndAliases[$alias];
        }

        return 'Common';
    }

    private static function entityTypes()
    {
        $entityTypeAliasesAndNames = [
            't'=>'Tax',
            'trg'=>'Tax Rules Group',
            'tr'=>'Tax Rule',
            'co'=>'Country',
            'st'=>'State',
            'c'=>'Category',
            'crr'=>'Carrier',
            'p'=>'Product',
            'atc'=>'Attachment',
            'prd'=>'Product Download',
            'spr'=>'Specific Price Rule',
            'spg'=> 'Specific Price Rule Condition Group',
            'spc'=>'Specific Price Rule Condition',
            'ag'=>'Attribute Group',
            'a'=>'Attribute',
            'com'=>'Combination',
            's'=>'Supplier',
            'm'=>'Manufacturer',
            'sp'=>'Specific Price',
            'i'=>'Image',
            'f'=>'Feature',
            'fv'=>'Feature Value',
            'cf'=>'Customization Field',
            'tag'=>'Tag',
            'cus'=>'Customer',
            'ct'=>'Customer Thread',
            'cm'=>'Customer Message',
            'car'=>'Cart',
            'e'=>'Employee',
            'adr'=>'Address',
            'o'=>'Order',
            'od'=>'Order Detail',
            'ort'=>'Order Return',
            'oh'=>'Order History',
            'osp'=>'Order Slip',
            'oi'=>'Order Invoice',
            'oc'=>'Order Carrier',
            'ocr'=>'Order Cart Rule',
            'op'=>'Order Payment',
            'om'=>'Order Message',
            'mes'=>'Message',
            'sa'=> 'Stock Available',
            'ps' => 'Product Supplier',
            'cms'=> 'CMS',
            'cro' => 'CMS Role',
            'ctg' => 'CMS Category',
            'cbl' => 'CMS Block',
            'cr' => 'Cart Rule',
            'cpg' => 'Cart Rule Product Rule Group',
            'cpr' => 'Cart Rule Product Rule',
            'met' => 'Meta',
            'war' => 'Warehouse',
            'stk' => 'Stock',
            'wpl' => 'Ware House Location',
            'zn' => 'Zone',
            'dlv' => 'Delivery',
            'rn' => 'Range Price',
            'rw' => 'Range Weight',
        ];

        return $entityTypeAliasesAndNames;
    }
}