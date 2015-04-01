<?php

interface DualMigrationInterface
{
    public function upMySQL();
    public function downMySQL();
    public function upSQLite();
    public function downSQLite();
}
