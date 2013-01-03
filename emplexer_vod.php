<?php 
	/**
	* 
	*/
	class EmplexerVod extends AbstractVod
	{
		
		function __construct()
		{
			hd_print(__METHOD__);
			parent::__construct(false, false, false);
		}
		
		public function try_load_movie($movie_id, &$plugin_cookies){
			hd_print(__METHOD__);
			hd_print(__METHOD__ . ': ' . $movie_id);
			$movie = new Movie(1);
			$movie->add_series_data(
				1,
				'teste serie',
				$movie_id,
				false
			);
			$this->set_cached_movie($movie);
		}
	}



	?>

