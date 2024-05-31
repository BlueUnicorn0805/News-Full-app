<?php

namespace App\Models;

use CodeIgniter\Model;

class Setting_Model extends Model
{

    protected $table = 'tbl_settings';
    protected $allowedFields = ['type', 'message'];

    public function downloadBackup()
    {
        $prefs = array(
            'tables' => array(),
            'ignore' => array(),
            'filename' => '',
            'format' => 'gzip',
            // gzip, zip, txt
            'add_drop' => TRUE,
            'add_insert' => TRUE,
            'newline' => "\n",
            'foreign_key_checks' => TRUE
        );
        if (count($prefs['tables']) === 0) {
            $prefs['tables'] = $this->db->listTables();
        }
        // Extract the prefs for simplicity
        extract($prefs);
        $output = '';
        // Do we need to include a statement to disable foreign key checks?
        if ($foreign_key_checks === FALSE) {
            $output .= 'SET foreign_key_checks = 0;' . $newline;
        }
        foreach ((array) $tables as $table) {
            // Is the table in the "ignore" list?
            if (in_array($table, (array) $ignore, TRUE)) {
                continue;
            }
            // Get the table schema
            $query = $this->db->query('SHOW CREATE TABLE ' . $this->db->escapeIdentifiers($this->db->database . '.' . $table));
            // No result means the table name was invalid
            if ($query === FALSE) {
                continue;
            }
            // Write out the table schema
            $output .= '#' . $newline . '# TABLE STRUCTURE FOR: ' . $table . $newline . '#' . $newline . $newline;

            if ($add_drop === TRUE) {
                $output .= 'DROP TABLE IF EXISTS ' . $this->db->protectIdentifiers($table) . ';' . $newline . $newline;
            }
            $i = 0;
            $result = $query->getResultArray();
            foreach ($result[0] as $val) {
                if ($i++ % 2) {
                    $output .= $val . ';' . $newline . $newline;
                }
            }
            // If inserts are not needed we're done...
            if ($add_insert === FALSE) {
                continue;
            }
            // Grab all the data from the current table
            $query = $this->db->query('SELECT * FROM ' . $this->db->protectIdentifiers($table));

            if ($query->getFieldCount() === 0) {
                continue;
            }
            // Fetch the field names and determine if the field is an
            // integer type. We use this info to decide whether to
            // surround the data with quotes or not
            $i = 0;
            $field_str = '';
            $isInt = array();
            while ($field = $query->resultID->fetch_field()) {
                // Most versions of MySQL store timestamp as a string
                $isInt[$i] = in_array($field->type, array(MYSQLI_TYPE_TINY, MYSQLI_TYPE_SHORT, MYSQLI_TYPE_INT24, MYSQLI_TYPE_LONG), TRUE);

                // Create a string of field names
                $field_str .= $this->db->escapeIdentifiers($field->name) . ', ';
                $i++;
            }
            // Trim off the end comma
            $field_str = preg_replace('/, $/', '', $field_str);
            // Build the insert string
            foreach ($query->getResultArray() as $row) {
                $valStr = '';
                $i = 0;
                foreach ($row as $v) {
                    if ($v === NULL) {
                        $valStr .= 'NULL';
                    } else {
                        // Escape the data if it's not an integer
                        $valStr .= ($isInt[$i] === FALSE) ? $this->db->escape($v) : $v;
                    }
                    // Append a comma
                    $valStr .= ', ';
                    $i++;
                }
                // Remove the comma at the end of the string
                $valStr = preg_replace('/, $/', '', $valStr);
                // Build the INSERT string
                $output .= 'INSERT INTO ' . $this->db->protectIdentifiers($table) . ' (' . $field_str . ') VALUES (' . $valStr . ');' . $newline;
            }
            $output .= $newline . $newline;
        }
        // Do we need to include a statement to re-enable foreign key checks?
        if ($foreign_key_checks === FALSE) {
            $output .= 'SET foreign_key_checks = 1;' . $newline;
        }
        return $output;
    }  

}
