<?php

function MRB_admin_areas(){
	global $admin_areas, $txt;
	loadPluginLanguage('CerealGuy:MoreRecyclingBins', 'lang/MoreRecyclingBins-Admin');
	$admin_areas['plugins']['areas']['morerecyclingbins'] = array(
							'label' => $txt['mrb_admin_label'],
							
							'function' => 'ManageMoreRecyclingBins',
							'permission' => array('admin_forum'),
							'icon' => 'packages_add.gif',
							'subsections' => array(
										'main' => array($txt['mrb_admin_main_label']),
										'add' => array($txt['mrb_admin_add_label']),
									),
							);


}

function ManageMoreRecyclingBins(){
	global $context, $txt;
	isAllowedTo('admin_forum');
	loadPluginLanguage('CerealGuy:MoreRecyclingBins', 'lang/MoreRecyclingBins-Admin');
	loadPluginTemplate('CerealGuy:MoreRecyclingBins', 'html/MoreRecyclingBins');
	loadPluginSource('CerealGuy:MoreRecyclingBins', 'Subs-MoreRecyclingBins');
	$context['mrb_errors'] = array();
	$context['mrb_success'] = array();
	$context['mrb_recyclingbins'] = get_morerecyclingbins();
	get_board_names();
 	$subActions = array(
                'main' => 'MainRecyclingBins',
		'add' => 'AddRecyclingBins',
		'delete' => 'DeleteRecyclingBins',
		'modify' => 'ModifyRecyclingBins',
	);

	$_REQUEST['sa'] = isset($_REQUEST['sa'], $subActions[$_REQUEST['sa']]) ? $_REQUEST['sa'] : 'main';
	// Set up the two tabs here...
	$context[$context['admin_menu_name']]['tab_data'] = array(
		'title' => $txt['mrb_admin_label'],
		'help' => $txt['mrb_admin_main_help'],
		'description' => $txt['mrb_admin_main_desc'],
		'tabs' => array(
			'main' => array(
				'description' => $txt['mrb_admin_main_desc'],
			),
			'add' => array(
				'description' => $txt['mrb_admin_add_desc'],
			),

			
		),
	);
	$subActions[$_REQUEST['sa']]();

}

function MainRecyclingBins(){
	global $context;
	wetem::load('mrb_main');
}

function AddRecyclingBins(){
	global $txt, $context, $settings;
	loadSource('ManageServer');
	get_inline_board_list();
	$context['mrb_modify'] = array('type' => 'add');
	if (isset($_GET['save']))
	{
		
		checkSession();
		$rb = (int)$_POST['select_board'];
		$rcbs = array();
		// Check childboards (ripped from saveDBSettings())
		if (isset($_POST['affected_boards']) && is_array($_POST['affected_boards']))
			
			foreach ($_POST['affected_boards'] as $invar => $on)
				if (isset($context['board_array'][$invar]))
					$rcbs[] = $invar;
		if(empty($rb))
			$context['mrb_errors'][] = $txt['mrb_error_no_recyclingboard_selected'];
		if(empty($rcbs))
			$context['mrb_errors'][] = $txt['mrb_error_no_recyclingboard_childs_selected'];

		if(isset($context['mrb_recyclingbins'][$rb]))
			$context['mrb_errors'][] = $context['board_names'][$rb]." is already a Recyclingboard. Please modify that one";

		if(!empty($rb))
			$context['mrb_modify']['recyclingboard'] = $rb; // set for template
		if(!empty($rcbs))
			$context['mrb_modify']['affected_boards'] = $rcbs;

		if(empty($context['mrb_errors'])){
			$rcbs = implode(',',$rcbs);
			wesql::insert('', '{db_prefix}morerecyclingboards', array('id_recyclingboard' => 'int', 'boards' => 'string'), array($rb, $rcbs));
			$context['mrb_success'][] = $txt['mrb_success_recyclingboard_added'];
		}


	}
	$context['page_title'] = $txt['mrb'];
	wetem::load('mrb_modify');
}

function DeleteRecyclingBins(){
	global $context, $txt;
	$delete_id = isset($_GET['id']) ? (int)$_GET['id'] : '';
	if(empty($delete_id))
		$context['mrb_errors'][] = $txt['mrb_error_no_recyclingboardid_set'];
	if(!isset($context['mrb_recyclingbins'][$delete_id]))
		$context['mrb_errors'][] = $txt['mrb_error_no_such_recyclingboard'];
	if(empty($context['mrb_errors'])){
		wesql::query('DELETE FROM {db_prefix}morerecyclingboards WHERE id_recyclingboard = {int:id_recyclingboard}', array('id_recyclingboard' => $delete_id));
		$context['mrb_recyclingbins'] = get_morerecyclingbins(); // update recyclingbins
		$context['mrb_success'][] = $txt['mrb_success_recyclingboard_deleted'];
	}	
	
	wetem::load('mrb_main');
}

function ModifyRecyclingBins(){
	global $context, $txt;
	$context['mrb_modify'] = array('type' => 'modify');

	$edit_id = isset($_GET['id']) ? (int)$_GET['id'] : '';
	if(empty($edit_id))
		$context['mrb_errors'][] = "No id set";
	if(!isset($context['mrb_recyclingbins'][$edit_id]))
		$context['mrb_errors'][] = $txt['mrb_error_no_such_recyclingboard'];

	if(empty($context['mrb_errors'])){
		// No errors we can proceed

		loadSource('ManageServer');
		get_inline_board_list();

		// Set stuff for template
		$context['mrb_modify']['recyclingboard'] = $edit_id;
		$context['mrb_modify']['affected_boards'] = $context['mrb_recyclingbins'][$edit_id];

		if (isset($_GET['save']))
		{
			
			checkSession();
			$rb = (int)$_POST['select_board'];
			$rcbs = array();
			// Check childboards (ripped from saveDBSettings())
			if (isset($_POST['affected_boards']) && is_array($_POST['affected_boards']))
				
				foreach ($_POST['affected_boards'] as $invar => $on)
					if (isset($context['board_array'][$invar]))
						$rcbs[] = $invar;
			if(empty($rb))
				$context['mrb_errors'][] = $txt['mrb_error_no_recyclingboard_selected'];
			if(empty($rcbs))
				$context['mrb_errors'][] = $txt['mrb_error_no_recyclingboard_childs_selected'];
	
			// update for template
			if(!empty($rb))
				$context['mrb_modify']['recyclingboard'] = $rb; 
			if(!empty($rcbs))
				$context['mrb_modify']['affected_boards'] = $rcbs;
	
			if(empty($context['mrb_errors'])){
				$rcbs = implode(',',$rcbs);
				wesql::query('UPDATE {db_prefix}morerecyclingboards SET id_recyclingboard={int:id_recyclingboard}, boards={string:boards} WHERE id_recyclingboard={int:id_recyclingboard_old}', array('id_recyclingboard' => $rb, 'boards' => $rcbs, 'id_recyclingboard_old' => $edit_id));
				$context['mrb_success'][] = $txt['mrb_success_recyclingboard_modified'];
			}


		}
		$context['page_title'] = $txt['mrb'];
		wetem::load('mrb_modify');

	}else{
		// Uuuuh errors... show main
		MainRecyclingBins();

	}
	

}
