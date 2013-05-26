<?php

namespace lib\dune\vod;

use Exception;
use PluginMovie;
use PluginVodInfo;
use PluginVodSeriesInfo;

class ShortMovie
{
    public $id;
    public $name;
    public $poster_url;

    public function __construct($id, $name, $poster_url)
    {
        if (is_null($id))
            throw new Exception("ShortMovie::id is null");

        $this->id = strval($id);
        $this->name = strval($name);
        $this->poster_url = strval($poster_url);
    }
}

class ShortMovieRange
{
    public $from_ndx;
    public $total;
    public $short_movies;

    public function __construct($from_ndx, $total, $short_movies = null)
    {
        $this->from_ndx = intval($from_ndx);
        $this->total = intval($total);
        $this->short_movies = $short_movies === null ?
            array() : $short_movies;
    }
}

class MovieSeries
{
    public $id;

    public function __construct($id)
    {
        if (is_null($id))
            throw new Exception("MovieSeries::id is null");

        $this->id = strval($id);
    }

    public $name = '';
    public $playback_url = '';
    public $playback_url_is_stream_url = true;
}

class Movie
{
    public $id;
    public $name = '';
    public $name_original = '';
    public $description = '';
    public $poster_url = '';
    public $length_min = -1;
    public $year = 0;
    public $directors_str = '';
    public $scenarios_str = '';
    public $actors_str = '';
    public $genres_str = '';
    public $rate_imdb = '';
    public $rate_kinopoisk = '';
    public $rate_mpaa = '';
    public $country = '';
    public $budget = '';

    public $series_list = null;

    public function __construct($id)
    {
        if (is_null($id))
            throw new Exception("Movie::id is null");

        $this->id = strval($id);
    }

    protected function to_string($v)
    {
        return $v === null ? '' : strval($v);
    }

    protected function to_int($v, $default_value)
    {
        $v = strval($v);
        if (!is_numeric($v))
            return $default_value;
        $v = intval($v);
        return $v <= 0 ? $default_value : $v;
    }

	/*
	 * remove linebreaks and html formatting
	 */ 
	public function clean_text($string)
	{
		//$arrToBeReplaced = array('\n', '\r');
		//$string = str_replace($arrToBeReplaced, '', $string);
		$string = strip_tags($string);
		$newstring = preg_replace("/[\n\r]/","", $string); 
		return $newstring;
	}

    public function set_data(
        $name,
        $name_original,
        $description,
        $poster_url,
        $length_min,
        $year,
        $directors_str,
        $scenarios_str,
        $actors_str,
        $genres_str,
        $rate_imdb,
        $rate_kinopoisk,
        $rate_mpaa,
        $country,
        $budget)
    {
        $this->name = $this->to_string($name);
        $this->name_original = $this->to_string($name_original);
        $this->description = $this->clean_text($this->to_string($description));
        $this->poster_url = $this->to_string($poster_url);
        $this->length_min = $this->to_int($length_min, -1);
        $this->year = $this->to_int($year, -1);
        $this->directors_str = $this->to_string($directors_str);
        $this->scenarios_str = $this->to_string($scenarios_str);
        $this->actors_str = $this->to_string($actors_str);
        $this->genres_str = $this->to_string($genres_str);
        $this->rate_imdb = $this->to_string($rate_imdb);
        $this->rate_kinopoisk = $this->to_string($rate_kinopoisk);
        $this->rate_mpaa = $this->to_string($rate_mpaa);
        $this->country = $this->to_string($country);
        $this->budget = $this->to_string($budget);

        $this->series_list = array();
    }

    public function add_series_data($id, $name,
        $playback_url, $playback_url_is_stream_url)
    {
        $series = new MovieSeries($id);

        $series->name = $this->to_string($name);
        $series->playback_url = $this->to_string($playback_url);
        $series->playback_url_is_stream_url =
            $playback_url_is_stream_url === true;

        $this->series_list[] = $series;
    }

    public function get_movie_array()
    {
        return array(
            PluginMovie::name => $this->name,
            PluginMovie::name_original => $this->name_original,
            PluginMovie::description => $this->description,
            PluginMovie::poster_url => $this->poster_url,
            PluginMovie::length_min => $this->length_min,
            PluginMovie::year => $this->year,
            PluginMovie::directors_str => $this->directors_str,
            PluginMovie::scenarios_str => $this->scenarios_str,
            PluginMovie::actors_str => $this->actors_str,
            PluginMovie::genres_str => $this->genres_str,
            PluginMovie::rate_imdb => $this->rate_imdb,
            PluginMovie::rate_kinopoisk => $this->rate_kinopoisk,
            PluginMovie::rate_mpaa => $this->rate_mpaa,
            PluginMovie::country => $this->country,
            PluginMovie::budget => $this->budget
        );
    }

    public function get_vod_info($sel_id, $buffering_ms)
    {
        if (!is_array($this->series_list) ||
            count($this->series_list) == 0)
        {
            throw new Exception('Invalid movie: series list is empty');
        }

        $series_array = array();
        $initial_series_ndx = -1;
        foreach ($this->series_list as $ndx => $series)
        {
            if (!is_null($sel_id) && $series->id === $sel_id)
                $initial_series_ndx = $ndx;

            $series_array[] = array(
                PluginVodSeriesInfo::name => $series->name,
                PluginVodSeriesInfo::playback_url => $series->playback_url,
                PluginVodSeriesInfo::playback_url_is_stream_url =>
                    $series->playback_url_is_stream_url,
            );
        }

        return array(
            PluginVodInfo::id => $this->id,
            PluginVodInfo::name => $this->name,
            PluginVodInfo::description => $this->description,
            PluginVodInfo::poster_url => $this->poster_url,
            PluginVodInfo::series => $series_array,
            PluginVodInfo::initial_series_ndx => $initial_series_ndx,
            PluginVodInfo::buffering_ms => $buffering_ms,
        );
    }
}

?>
