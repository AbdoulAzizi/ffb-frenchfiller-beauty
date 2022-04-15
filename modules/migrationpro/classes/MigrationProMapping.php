<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from MigrationPro MMC
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the MigrationPro MMC is strictly forbidden.
 * In order to obtain a license, please contact us: migrationprommc@gmail.com
 *
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise a une licence commerciale
 * concedee par la societe MigrationPro MMC
 * Toute utilisation, reproduction, modification ou distribution du present
 * fichier source sans contrat de licence ecrit de la part de la MigrationPro MMC est
 * expressement interdite.
 * Pour obtenir une licence, veuillez contacter la MigrationPro MMC a l'adresse: migrationprommc@gmail.com
 *
 *  @author    Edgar I.
 * @copyright Copyright (c) 2012-2016 MigrationPro MMC
 * @license   Commercial license
 * @package   MigrationPro: Prestashop To PrestaShop
 */

class MigrationProMapping extends ObjectModel
{
    public $id;
    public $type;
    public $source_id;
    public $source_name;
    public $local_id;

    public static $definition = array(
        'table'   => 'migrationpro_mapping',
        'primary' => 'id_mapping',
        'fields'  => array(
            'type'        => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
            'source_id'   => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'source_name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'local_id'    => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId')
        ),
    );

    public static function listMapping($list = false, $keyAsSourceId = false)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('migrationpro_mapping');
        $mappings = array();
        $rows = Db::getInstance()->executeS($sql);
        if (!$list) {
            return $rows;
        }

        if ($keyAsSourceId) {
            foreach ($rows as $row) {
                    $mappings[$row['type']][$row['source_id']] = $row['local_id'];
            }
        } else {
            foreach ($rows as $row) {
                $mappings[$row['type']][$row['id_mapping']] = array(
                    'id_mapping'  => $row['id_mapping'],
                    'source_id'   => $row['source_id'],
                    'source_name' => $row['source_name'],
                    'local_id'    => $row['local_id']
                );
            }
        }

        return $mappings;
    }

    public static function getMapTypeCount($entity_type)
    {
        $sql = 'SELECT count(*) FROM ' . _DB_PREFIX_ . 'migrationpro_mapping as map WHERE map.type=\'' . pSQL($entity_type) . '\' ';
        return Db::getInstance()->getValue($sql);
    }
}
