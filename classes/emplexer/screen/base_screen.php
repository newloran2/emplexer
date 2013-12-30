<?php

/**
* Base Menu class for emplexer
*/

abstract class BaseScreen
{

	protected $iconFile = "gui_skin://small_icons/folder.aai";
	protected $openFolder = PLUGIN_OPEN_FOLDER_ACTION_ID;
	protected $handlerUserInput = PLUGIN_HANDLE_USER_INPUT_ACTION_ID;
	protected $path;
	protected $data;
	protected $nextTemplate = false;
	protected $templates =array();

	function __construct($key=null, $nextTemplate=false) {
		if (!$key)
			$key = '/library/sections';

		$this->path = Client::getInstance()->getUrl(null, $key);
		$this->data = Client::getInstance()->getAndParse($this->path);
		$this->nextTemplate = $nextTemplate != null ? $nextTemplate :  array();

		$this->templates = json_decode(Config::getInstance()->templateViewNumber);

		//hd_print(__METHOD__ .  ':' .  print_r($this->templates, true));

	}

	public function getTemplateByType($type){

		$fun = 'template'.ucwords($type);
		return $this->$fun();
	}

	private function getTemplateIndexAndUpdate($key){
		$templateIndex = Config::getInstance()->templateViewNumber;
		$templateIndex != null ? json_decode($templateIndex) :  array();
		$index = isset($templateIndex->{$key}) ? (int)$templateIndex->{$key}+1 : 1;
		$templateIndex->{$key} = $index;
		Config::getInstance()->templateViewNumber = json_encode($templateIndex);

		return $index;

	}

	public function genericTemplate()
	{
		$itens = array();
		$folderItems = array();
		foreach ($this->data as $item)
		{
			$folderItems[] = array(
				PluginRegularFolderItem::media_url			=> Client::getInstance()->getUrl($this->path , (string)$item->attributes()->key),
				PluginRegularFolderItem::caption			=> isset($item->attributes()->caption)? (string)$item->attributes()->caption : (string)	$item->attributes()->title ,
				PluginRegularFolderItem::view_item_params	=> array(
					ViewItemParams::icon_path => Client::getInstance()->getThumbUrl((string)$item->attributes()->thumb)
				)
			);
		}

		$availableTemplates = array (
				array(
					PluginRegularFolderView::async_icon_loading 			=> true,
					PluginRegularFolderView::initial_range					=>
					array(
						PluginRegularFolderRange::items							=>	$folderItems,
						PluginRegularFolderRange::total							=>	count($folderItems),
						PluginRegularFolderRange::count							=>	count($folderItems),
						PluginRegularFolderRange::more_items_available			=>	false,
						PluginRegularFolderRange::from_ndx						=>	0
						),
					PluginRegularFolderView::view_params					=> array(
						ViewParams::paint_icon_selection_box   => false,
		 				ViewParams::num_cols => 5,
		                ViewParams::num_rows => 2,
		                ViewParams::paint_sandwich => false,
		                ViewParams::sandwich_base => 'gui_skin://special_icons/sandwich_base.aai',
		                ViewParams::sandwich_mask => 'cut_icon://{name=sandwich_mask}',
		                ViewParams::sandwich_cover => 'cut_icon://{name=sandwich_cover}',
		                ViewParams::sandwich_width => 200,
		                ViewParams::sandwich_height => 300,
		                ViewParams::sandwich_icon_upscale_enabled => true,
		                ViewParams::sandwich_icon_keep_aspect_ratio => true,
		                //ViewParams::icon_selection_box_width => 180, //150,
		                //ViewParams::icon_selection_box_height => 333, //222,
		                ViewParams::paint_details => true,
		                ViewParams::zoom_detailed_icon => true,
		                ViewParams::paint_item_info_in_details => true,
		                ViewParams::item_detailed_info_font_size => FONT_SIZE_SMALL,
		                ViewParams::optimize_full_screen_background => false,
		                ViewParams::background_order => 'before_all'
		            ),
					PluginRegularFolderView::base_view_item_params			=> array(
						ViewItemParams::item_padding_top => 0,
					    ViewItemParams::item_padding_bottom => 0,
					    ViewItemParams::icon_valign => VALIGN_CENTER,
					    ViewItemParams::item_paint_caption => false,
					    ViewItemParams::icon_scale_factor => 1.0,
		                ViewItemParams::icon_sel_scale_factor => 1.2,
					),
					PluginRegularFolderView::not_loaded_view_item_params	=>	array(
						ViewItemParams::icon_path => 'plugin_file://icons/poster.png',
					    ViewItemParams::item_detailed_icon_path => 'plugin_file://icons/poster.png',
					    ViewItemParams::item_paint_caption_within_icon => true,
					    ViewItemParams::item_caption_within_icon_color => 'white',
					    ViewItemParams::item_caption_font_size => FONT_SIZE_SMALL
					),
					PluginRegularFolderView::actions =>array(
						GUI_EVENT_KEY_ENTER	=> array(GuiAction::handler_string_id=>	$this->openFolder)
					)
				),
				array (
					PluginFolderView::view_kind								=>	PLUGIN_FOLDER_VIEW_REGULAR,
					PluginFolderView::data									=> array(
					PluginRegularFolderView::initial_range					=>
						array(
							PluginRegularFolderRange::items							=>	$folderItems,
							PluginRegularFolderRange::total							=>	count($folderItems),
							PluginRegularFolderRange::count							=>	count($folderItems),
							PluginRegularFolderRange::more_items_available			=>	false,
							PluginRegularFolderRange::from_ndx						=>	0
							),
						PluginRegularFolderView::view_params					=>
						array(
							ViewParams::num_cols									=>	1,
							ViewParams::num_rows									=>	11
							),
						PluginRegularFolderView::base_view_item_params			=>
						array(
							ViewItemParams::item_paint_icon => FALSE,
			                    ViewItemParams::icon_scale_factor => 0.75,
			                    ViewItemParams::icon_sel_scale_factor => 1,
			                    ViewItemParams::icon_path => $this->iconFile,
			                    ViewItemParams::item_layout => HALIGN_LEFT,
			                    ViewItemParams::icon_valign => VALIGN_CENTER,
			                    ViewItemParams::item_caption_font_size => FONT_SIZE_NORMAL
							),
						PluginRegularFolderView::not_loaded_view_item_params	=>	array(),
						PluginRegularFolderView::actions						=>
						array(
							GUI_EVENT_KEY_ENTER										=>
							array(
								GuiAction::handler_string_id							=>	$this->openFolder
								)
						)
					)
				)
			);


		// if (!isset($this->nextTemplate) || $this->nextTemplate == null ){
		// 	$this->nextTemplate->{__FUNCTION__} = 0;
		// }else if ($this->nextTemplate){
		// 	$this->templates->{__FUNCTION__} +=1;
		// }
		// if (!isset($this->templates->{__FUNCTION__}) && $this->templates->{__FUNCTION__} >= count($availableTemplates)){
		// 	$this->templates->{__FUNCTION__} = 0;
		// }

		//echo ("index = " . $this->templates->{__FUNCTION__});



		return array(
			PluginFolderView::view_kind								=>	PLUGIN_FOLDER_VIEW_REGULAR,
			PluginFolderView::data									=> $availableTemplates[0]
		);
	}

	//without 	viewGroup i considerer that screnn a generic with directories index
	protected function template(){
		$itens = array();
		$folderItems = array();
		foreach ($this->data as $item)
		{
			$folderItems[] = array(
				PluginRegularFolderItem::media_url			=> $this->path . "/" .(string)$item->attributes()->key,
				PluginRegularFolderItem::caption			=> isset($item->attributes()->caption)? (string)$item->attributes()->caption : (string)	$item->attributes()->title,
				PluginRegularFolderItem::view_item_params	=> array()
			);
		}

		return array(
		PluginFolderView::view_kind								=>	PLUGIN_FOLDER_VIEW_REGULAR,
		PluginFolderView::data									=>
		array(
			PluginRegularFolderView::initial_range					=>
			array(
				PluginRegularFolderRange::items							=>	$folderItems,
				PluginRegularFolderRange::total							=>	count($folderItems),
				PluginRegularFolderRange::count							=>	count($folderItems),
				PluginRegularFolderRange::more_items_available			=>	false,
				PluginRegularFolderRange::from_ndx						=>	0
				),
			PluginRegularFolderView::view_params					=>
			array(
				ViewParams::num_cols									=>	1,
				ViewParams::num_rows									=>	11
				),
			PluginRegularFolderView::base_view_item_params			=>
			array(
				ViewItemParams::item_paint_icon => FALSE,
                    ViewItemParams::icon_scale_factor => 0.75,
                    ViewItemParams::icon_sel_scale_factor => 1,
                    ViewItemParams::icon_path => $this->iconFile,
                    ViewItemParams::item_layout => HALIGN_LEFT,
                    ViewItemParams::icon_valign => VALIGN_CENTER,
                    ViewItemParams::item_caption_font_size => FONT_SIZE_NORMAL
				),
			PluginRegularFolderView::not_loaded_view_item_params	=>	array(),
			PluginRegularFolderView::actions						=>
			array(
				GUI_EVENT_KEY_ENTER										=>
				array(
					GuiAction::handler_string_id							=>	$this->openFolder
					)
				)
			)
		);
	}
	protected function templateSecondary(){
		return $this->template();
	}
	protected function templateMovie(){
		//hd_print("\n\nTest = " . __FUNCTION__);
		$a =  $this->genericTemplate();
		// $a['data']['view_params']['num_rows'] = 5	;
        $a['data']['actions']['key_enter']['handler_string_id'] = 'plugin_handle_user_input';



		return $a;
	}

	protected function templateShow(){
		return $this->genericTemplate();
	}

	protected function templateSeason(){
		return $this->genericTemplate();
	}

	protected function templateEpisode(){
		$a =  $this->genericTemplate();


		$a['data']['base_view_item_params'] = array
         (
            ViewItemParams::item_padding_top => 0,
            ViewItemParams::item_padding_bottom => 0,
            ViewItemParams::icon_valign => VALIGN_CENTER,
            ViewItemParams::item_paint_caption => false,
            ViewItemParams::icon_width => 190,
            ViewItemParams::icon_height => 110,
            ViewItemParams::icon_scale_factor => 1.0,
            ViewItemParams::icon_sel_scale_factor => 1.2,
        );


        $a['data']['view_params']['num_rows'] = 5;
        $a['data']['actions']['key_enter']['handler_string_id'] = 'plugin_handle_user_input';



		return $a;
	}

	protected function templateArtist(){

		$a = $this->genericTemplate();
		$a['data']['view_params']['num_rows'] = 3;
		$a['data']['base_view_item_params'] = array
         (
            ViewItemParams::item_padding_top => 0,
            ViewItemParams::item_padding_bottom => 0,
            ViewItemParams::icon_valign => VALIGN_CENTER,
            ViewItemParams::item_paint_caption => false,
            ViewItemParams::icon_width => 190,
            ViewItemParams::icon_height => 190,
            ViewItemParams::icon_scale_factor => 1.0,
            ViewItemParams::icon_sel_scale_factor => 1.2,
        );

        $a['data']['not_loaded_view_item_params'] = array(
			ViewItemParams::icon_path => 'plugin_file://icons/album.png',
		    ViewItemParams::item_detailed_icon_path => 'plugin_file://icons/album.png',
		    ViewItemParams::icon_keep_aspect_ratio =>  false,
		    ViewItemParams::item_paint_caption_within_icon => true,
		    ViewItemParams::item_caption_within_icon_color => 'white',
		    ViewItemParams::item_caption_font_size => FONT_SIZE_SMALL
		);
         return $a;
	}


	protected function templateAlbum()
	{

		$a = $this->template();
		$a['data']['actions']['key_enter']['handler_string_id'] = 'plugin_handle_user_input';

		return $a;

	}
	protected function templateTrack()
	{
		return $this->templateArtist();
	}
	protected function generateSingleList($items){}

	public function __destruct()
	{
		Config::getInstance()->templateViewNumber = json_encode($this->templates);
	}
	abstract public function generateScreen();

}


?>