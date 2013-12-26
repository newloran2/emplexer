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
	function __construct($key=null) {
		if (!$key)
			$key = '/library/sections';
		$this->path = $key;
		$this->data = Client::getInstance()->getByPath($this->path);		
	}	

	public function getTemplateByType($type){
		$fun = 'template'.ucwords($type);
		return $this->$fun();
	}

	//without 	viewGroup i considerer that screnn a generic with directories index
	protected function template(){
		$itens = array();
		$folderItems = array();
		foreach ($this->data as $item)
		{
			$folderItems[] = array(
				PluginRegularFolderItem::media_url			=> $this->path . "/" .(string)$item->attributes()->key,
				PluginRegularFolderItem::caption			=> isset($item->attributes()->caption)? (string)$item->attributes()->caption : (string)$item->attributes()->title ,
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
	protected function templateSecondary(){
		return $this->template();
	}
	protected function templateMovie(){
		# code...
	}
	protected function templateSeason(){
		# code...
	}

	protected function templateEpisode(){
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