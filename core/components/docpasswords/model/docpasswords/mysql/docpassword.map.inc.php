<?php
$xpdo_meta_map['Docpassword']= array (
  'package' => 'docpasswords',
  'table' => 'docpassword',
  'fields' => 
  array (
    'id' => NULL,
    'document_id' => NULL,
    'password' => NULL,
    'datetime_created' => NULL,
    'datetime_modified' => 'CURRENT_TIMESTAMP',
  ),
  'fieldMeta' => 
  array (
    'id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
      'generated' => 'native',
    ),
    'document_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => true,
    ),
    'password' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
    ),
    'datetime_created' => 
    array (
      'dbtype' => 'timestamp',
      'phptype' => 'string',
      'null' => true,
    ),
    'datetime_modified' => 
    array (
      'dbtype' => 'timestamp',
      'phptype' => 'string',
      'null' => false,
      'default' => 'CURRENT_TIMESTAMP',
    ),
  ),
  'indexes' => 
  array (
    'PRIMARY' => 
    array (
      'alias' => 'PRIMARY',
      'primary' => true,
      'unique' => true,
      'columns' => 
      array (
        'id' => 
        array (
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
);
