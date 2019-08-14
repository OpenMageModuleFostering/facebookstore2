<?php
/**
 * API user setup script
 *
 * @category    Shopgo
 * @package     Shopgo_FacebookStore
 */

$role = Mage::getModel('api/roles')
    ->setName('Shopgo')
    ->setRoleType('G')
    ->save();

Mage::getModel("api/rules")
    ->setRoleId($role->getId())
    ->setResources(array('all'))
    ->saveRel();


$data = array(
    'username'  => 'shopgo_facebookstore',
    'firstname' => 'shopgo_facebookstore',
    'lastname'  => 'shopgo_facebookstore',
    'email'     => 'support@shopgo.me',
    'api_key'   => 'JG0kDQ569Ls26by',
    'api_key_confirmation'  => 'JG0kDQ569Ls26by',
    'is_active' => '1',
);
$user = Mage::getModel('api/user')->setData($data)
    ->save();

$user->setRoleIds(array($role->getId()))
    ->setRoleUserId($user->getUserId())
    ->saveRelations();
