<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

class Migration20260702145000ComProjects extends Base
{
    public function up()
    {
        $table = '#__projects_connections';
        if ($this->db->tableExists($table) && !$this->db->tableHasField($table, 'creator_id'))
        {
            $query = "ALTER TABLE `$table` ADD COLUMN `creator_id` INT(11) NOT NULL DEFAULT '0'";
            $this->db->setQuery($query);
            $this->db->query();
            $this->log("Added column `creator_id` to $table");
        }
    }

    public function down()
    {
        $table = '#__projects_connections';
        if ($this->db->tableExists($table) && $this->db->tableHasField($table, 'creator_id'))
        {
            $query = "ALTER TABLE `$table` DROP COLUMN `creator_id`";
            $this->db->setQuery($query);
            $this->db->query();
            $this->log("Dropped column `creator_id` from $table");
        }
    }
}