<?php

require_once System::getIncLoginDir() . 'libs/REDBObjects/mysql/IsMySQLListClass.class.php';

/**
 * To fix the issue with IsMySQLListClass where the add method doesn't set the
 * value of the second table's primary key.
 */
class IsMySQLListClassFix extends IsMySQLListClass
{
    public function add(IIsDBClass $object, $append = false)
    {
        /** @var $object IsMySQLClass */
        $object->update(false);
        //ciklusban végigmegy a táblákon
        $props = $object->properties;
        $first = true;
        $last_id = 0;
        foreach ($props as $tableName=>&$fields)
        {
            //újabb ciklusban a mezőkön
            $fieldNames = array();
            $fieldValues = array();
            foreach ($fields as $fieldName => $fieldValue)
            {
                //a mezőneveket és az értékeiket külön tömbbe tölti.
                $fieldNames[] = "`$fieldName`";
                if (!$object->isNonQuoted($tableName, $fieldName)) {
                    $fieldValue = "'$fieldValue'";
                }

                $fieldValues[] = $fieldValue;
            }

            if (!$first and $last_id > 0) {
                $tableNames = array_keys($props);
                foreach ($object->getPriKeys($tableNames[0]) as $pkn => $pkv) {
                    $fieldNames[] = "`$pkn`";
                    $fieldValues[] = $last_id;
                }
            }

            //vesszővel elválasztott formátumba konvertálja az értékek és nevek tömbjeit
            $fieldValues = implode(', ',$fieldValues);
            $fieldNames = implode(', ',$fieldNames);
            //felviszi a táblába az új sort a megadott mezőkkel
            $t = isset($this->tableAliases[$tableName]) ? $this->tableAliases[$tableName] : $tableName;
            mysql_query("insert into `$t` ($fieldNames) values($fieldValues)");
            if ($first)
            {
                $first = false;
                //szükség lehet az elsődleges kulcsra, ha a kapcsoló mező auto_increment
                $last_id = mysql_insert_id();
            }
        }
        $this->count++;
        if ($append)
        {
            $this->records[] = $object;
        }
        return $last_id;
    }
}
