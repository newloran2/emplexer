<?php 
	/**
	* 
	*/
	class EmplexerVod extends AbstractVod
	{

		private $base_url;
		function __construct()
		{
			hd_print(__METHOD__);
			parent::__construct(false, false, false);
		}
		
		public function try_load_movie($movie_id, &$plugin_cookies){
			hd_print(__METHOD__ ) ;

			if (!$this->base_url){
				$this->base_url = EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this);
			}
			$url = $this->base_url  . $movie_id;
			$xml =  HD::getAndParseXmlFromUrl($url);


			$movie = new Movie($movie_id);

			// $movie->set_data("American",'The American','Alone among assassins, Jack is a master craftsman. When a job in Sweden ends more harshly than expected for this American abroad, he vows to his contact Larry that his next assignment will be his last. Jack reports to the Italian countryside, where he holes up in a small town and relishes being away from death for a spell. The assignment, as specified by a Belgian woman, Mathilde, is in the offing as a weapon is constructed. Surprising himself, Jack seeks out the friendship of local priest Father Benedetto and pursues romance with local woman Clara. But by stepping out of the shadows, Jack may be tempting fate.','http://192.168.2.9:32400/library/metadata/4376/thumb/1342928192',1050,'2010','Anton Corbijn','Hiporã',
				// "George Clooney \n Jack,Thekla Reuten|Mathilde,Bruce Altman|Larry,Violante Placido|Clara|,Paolo Bonacelli|Father Benedetto",'Crime,Drama,Suspense,Thriller',6.4000000953674299,10,'R','USA','1 real');
			

// (string)$xml->Video->attributes()->summary
			$movie->set_data(
				$name = (string)$xml->Video->attributes()->grandparentTitle ,
				$name_original = (string)$xml->Video->attributes()->title ,
				$description =  (string)$xml->Video->attributes()->summary ,
				$poster_url = $this->base_url . (string)$xml->Video->attributes()->thumb  ,
				$length_min = ((float)$xml->Video->attributes()->duration) /1000/60 ,
				$year = (string)$xml->Video->attributes()->year,
				$directors_str =  $this->getWriterStr($xml),
				$scenarios_str = null,
				$actors_str = $this->getRolesStr($xml),
				$genres_str = $this->getGenreStr($xml) ,
				$rate_imdb = (float)$xml->Video->attributes()->rating ,
				$rate_kinopoisk = null ,
				$rate_mpaa = (string)$xml->Video->attributes()->contentRating ,
				$country = $this->getCountryStr($xml),
				$budget = null

				);
			$movie->add_series_data(1,$movie->name,'nfs://192.168.2.9:'. (string) $xml->Video->Media->Part->attributes()->file ,true);

			hd_print(__METHOD__ . ':' . print_r($movie, true));
			$this->set_cached_movie($movie);
			hd_print(print_r($this,true));
		}

		public function getWriterStr(&$xml)
		{
			$writes = null;
			// hd_print(__METHOD__ . ':' . print_r($xml, true));
			foreach ($xml->Video->Writer as $writer) {
				$writes .= (string)$writer->attributes()->tag . "\n";
			}
			return $writes;
		}

		public function getRolesStr(&$xml)
		{
			$roles = null;

			foreach ($xml->Video->Role as $role) {
				$roles .= (string)$role->attributes()->tag  .  (!$role->attributes()->role ?"\n" :  ' as ' . (string)$role->attributes()->role . "\n" );
			}
			return $roles;
		}

		public function getGenreStr(&$xml)
		{
			$genres = null;

			foreach ($xml->Video->Genre as $genre) {
				$genres .= (string)$genre->attributes()->tag  . ", ";
			}
			return $genres;
		}
		public function getCountryStr(&$xml)
		{
			$countries = null;

			foreach ($xml->Country as $country) {
				$countries .= (string)$country->attributes()->tag .  "\n";
			}
			return $countries;
		}

		public function get_vod_info(MediaURL $media_url, &$plugin_cookies){
			$movie = $this->get_cached_movie($media_url->movie_id);
			hd_print(__METHOD__ . ':'  . print_r($movie, true));
			

		// $params['selected_media_url'] = $toPlay->selected_media_url;
			$series_array = array();
			$series_array[] = array(
				PluginVodSeriesInfo::name => $movie->name_original . "\n" .  $movie->name,
				PluginVodSeriesInfo::playback_url => $movie->series_list[0]->playback_url,
				PluginVodSeriesInfo::playback_url_is_stream_url => true,
				);

			$toBeReturned = array(
				PluginVodInfo::id => 1,
				PluginVodInfo::series => $movie->series_list,
				PluginVodInfo::name =>  $movie->name_original,
				PluginVodInfo::description => $movie->description,
				PluginVodInfo::poster_url => $movie->poster_url,
				PluginVodInfo::initial_series_ndx => 0,
				PluginVodInfo::buffering_ms => 6000,
				// PluginVodInfo::initial_position_ms =>$media_url->viewOffset,
				PluginVodInfo::advert_mode => false,
				// PluginVodInfo::timer =>  array(GuiTimerDef::delay_ms => 5000),
			/*PluginVodInfo::actions => array(
				GUI_EVENT_PLAYBACK_STOP => UserInputHandlerRegistry::create_action($this, 'enter', $params),
				)*/
			);	

		hd_print(__METHOD__ . ':'  . print_r($toBeReturned, true));

			return $toBeReturned;
		}





	}



	?>

