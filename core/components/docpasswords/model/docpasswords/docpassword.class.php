<?php
/*
 * @author     Arietis Software <code@arietis-software.com>
 * @copyright  Copyright (c) 2011 Arietis Software Innovations
 * @license    http://www.software.com/license/gnu/license.txt   GNU License Version 3
 */
class Docpassword extends xPDOSimpleObject
{

    public function __construct(& $xpdo)
    {
        parent :: __construct($xpdo);
    }

    /**
     * Overrides xPDOObject::save to add edited/created auto-filling fields
     *
     * {@inheritDoc}
     */
    public function save($cacheFlag = null) {

        $this->set('datetime_created', date('Y-m-d H:i:s', strtotime('NOW')));

        $this->set('datetime_modified', date('Y-m-d H:i:s', strtotime('NOW')));
        
        //
        return parent :: save($cacheFlag);
    }
}