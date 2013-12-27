<?php 

/**
* Base Menu class for emplexer
*/
abstract class BaseScreen
{
	protected $iconFile = "gui_skin://small_icons/folder.aai";
	protected $action = PLUGIN_OPEN_FOLDER_ACTION_ID;
	protected $path;
	protected $data;
	protected $nextTemplate = false;
	function __construct($key=null, $nextTemplate=false) {
		if (!$key)
			$key = '/library/sections';
		$this->path = $key;
		$this->data = Client::getInstance()->getByPath($this->path);		
		$this->nextTemplate = $nextTemplate;
	}	

	public function getTemplateByType($type){

		// print_r($this);
		$fun = 'template'.ucwords($type);
		$v =  isset(Config::getInstance()->templateViewNumber) ? Config::getInstance()->templateViewNumber : 0;
		$val = $this->nextTemplate ?  $this->getTemplateIndexAndUpdate($fun) : $v;
		// echo "valor = $val \n\n\n";
		return $this->$fun($val);
	}

	private function getTemplateIndexAndUpdate($key){
		$templateIndex = Config::getInstance()->templateViewNumber;
		$templateIndex != null ? json_decode($templateIndex) :  array();
		$index = isset($templateIndex->{$key}) ? (int)$templateIndex->{$key}+1 : 1;
		$templateIndex->{$key} = $index;
		// print_r(Config::getInstance());
		Config::getInstance()->templateViewNumber = json_encode($templateIndex);

		// print_r(Config::getInstance());
		echo "index== $index \n\n\n";
		return $index;

	}

	//without 	viewGroup i considerer that screnn a generic with directories index
	protected function template($templateIndex=0){
		$itens = array();
		$folderItems = array();
		foreach ($this->data as $item)
		{
			$folderItems[] = array(
				PluginRegularFolderItem::media_url			=> $this->path . "/" .(string)$item->attributes()->key,
				PluginRegularFolderItem::caption			=> isset($item->attributes()->caption)? (string)$item->attributes()->caption : (string)	$item->attributes()->title ,
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
					GuiAction::handler_string_id							=>	$this->action
					)
				)
			)
		);
	}	
	protected function templateSecondary($templateIndex=0){
		return $this->template();
	}
	protected function templateMovie($templateIndex=0){
		echo ("templateIndex= " . $templateIndex . " \n"); 
		$itens = array();
		$folderItems = array();
		foreach ($this->data as $item)
		{
			$folderItems[] = array(
				PluginRegularFolderItem::media_url			=> $this->path . "/" .(string)$item->attributes()->key,
				PluginRegularFolderItem::caption			=> isset($item->attributes()->caption)? (string)$item->attributes()->caption : (string)	$item->attributes()->title ,
				PluginRegularFolderItem::view_item_params	=> array()
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
		 				ViewParams::num_cols => 5,
		                ViewParams::num_rows => 2,
		                ViewParams::paint_sandwich => true,
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
		                ViewItemParams::icon_sel_scale_factor => 1.1, //1.2,	
					),
					PluginRegularFolderView::not_loaded_view_item_params	=>	array(
						ViewItemParams::icon_path => 'plugin_file://icons/poster.png',
					    ViewItemParams::item_detailed_icon_path => 'plugin_file://icons/poster.png',
					    ViewItemParams::item_paint_caption_within_icon => true,
					    ViewItemParams::item_caption_within_icon_color => 'white',
					    ViewItemParams::item_caption_font_size => FONT_SIZE_SMALL			   
					),
					PluginRegularFolderView::actions =>array(
						GUI_EVENT_KEY_ENTER	=> array(GuiAction::handler_string_id=>	$this->action)
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
								GuiAction::handler_string_id							=>	$this->action
								)
						)
					)
				)
			);


		return array(
			PluginFolderView::view_kind								=>	PLUGIN_FOLDER_VIEW_REGULAR,
			PluginFolderView::data									=> $availableTemplates[$templateIndex]
		);
	}
	protected function templateSeason($templateIndex=0){
		# code...
	}

	protected function templateEpisode($templateIndex=0){
		# code...
	}

	protected function generateSingleList($items)
	{
		$folderItems = array();
		foreach ($items as $item)
		{
			$folderItems[] = array(
				PluginRegularFolderItem::media_url			=> $item["url"],
				PluginRegularFolderItem::caption			=> $item["caption"],
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
					GuiAction::handler_string_id							=>	$this->action
					)
				)
			)
		);
	}
	abstract public function generateScreen();
}


?>