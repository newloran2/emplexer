<?php

require_once 'lib/archive.php';

class ArchiveCache
{
    private static $archive_by_id;

    public static function set_archive(Archive $archive)
    {
        self::$archive_by_id[$archive->get_id()] = $archive;
    }

    public static function get_archive_by_id($id)
    {
        if (is_null(self::$archive_by_id))
            return null;
        return self::$archive_by_id[$id];
    }

    public static function clear_archive($id)
    {
        if (is_null(self::$archive_by_id))
            return;

        unset(self::$archive_by_id[$id]);
    }

    public static function clear_all()
    {
        self::$archive_by_id = null;
    }
}

?>
