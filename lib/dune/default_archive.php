<?php

// require_once 'lib/archive_cache.php';

class DefaultArchive implements Archive
{
    public static function clear_cache()
    { ArchiveCache::clear_all(); }

    public static function clear_cached_archive($id)
    { ArchiveCache::clear_archive($id); }

    public static function get_cached_archive($id)
    { return ArchiveCache::get_archive_by_id($id); }

    public static function get_archive($id, $url_prefix)
    {
        $archive = ArchiveCache::get_archive_by_id($id);
        if (!is_null($archive))
            return $archive;

        $version_url = $url_prefix . '/versions.txt';
        try
        {
            $doc = HD::http_get_document($version_url);
        }
        catch (Exception $e)
        {
            $doc = null;
        }

        $version_by_name = array();
        $total_size = 0;

        if (is_null($doc))
        {
            hd_print("Failed to fetch archive versions.txt from $version_url.");
        }
        else
        {
            $tok = strtok($doc, "\n");
            while ($tok !== false)
            {
                $pos = strrpos($tok, ' ');
                if ($pos === false)
                {
                    hd_print("Invalid line in versions.txt for archive '$id'.");
                    continue;
                }

                $name = trim(substr($tok, 0, $pos));
                $version = trim(substr($tok, $pos + 1));
                $version_by_name[$name] = $version;

                $tok = strtok("\n");
            }

            hd_print("Archive $id: " . count($version_by_name) . " files.");

            $size_url = $url_prefix . '/size.txt';
            $doc = HD::http_get_document($size_url);
            if (is_null($doc))
            {
                hd_print("Failed to fetch archive size.txt from $size_url.");

                $version_by_name = array();
            }
            else
            {
                $total_size = intval($doc);
                hd_print("Archive $id: size = $total_size");
            }
        }

        $archive = new DefaultArchive($id,
            $url_prefix, $version_by_name, $total_size);

        ArchiveCache::set_archive($archive);

        return $archive;
    }

    

    private $id;
    private $url_prefix;
    private $version_by_name;
    private $total_size;

    public function __construct($id,
        $url_prefix, $version_by_name, $total_size)
    {
        $this->id = $id;
        $this->url_prefix = $url_prefix;
        $this->version_by_name = $version_by_name;
        $this->total_size = $total_size;
    }

    public function get_id()
    { return $this->id; }

    public function get_archive_def()
    {
        $urls_with_keys = array();
        foreach ($this->version_by_name as $name => $version)
        {
            $pos = strrpos($name, ".");
            if ($pos === false)
                $key = $name . '.' . $version;
            else
            {
                $key = substr($name, 0, $pos) . '.' .
                    $version . substr($name, $pos);
            }

            $url = $this->url_prefix . "/" . $name;
            $urls_with_keys[$key] = $url;
        }

        return array
        (
            PluginArchiveDef::id => $this->id,
            PluginArchiveDef::urls_with_keys => $urls_with_keys,
            PluginArchiveDef::all_tgz_url => $this->url_prefix . "/all.tgz",
            PluginArchiveDef::total_size => $this->total_size,
        );
    }

    public function get_archive_url($name)
    {
        if (!isset($this->version_by_name[$name]))
            return "missing://";
        $version = $this->version_by_name[$name];

        $pos = strrpos($name, ".");
        if ($pos === false)
            $key = $name . '.' . $version;
        else
        {
            $key = substr($name, 0, $pos) . '.' .
                $version . substr($name, $pos);
        }

        return 'plugin_archive://' . $this->id . '/' . $key;
    }
}

?>
