<?php

function get_morerecyclingbins(){
	$request = wesql::query('SELECT id_recyclingboard, boards FROM {db_prefix}morerecyclingboards', array());
	$recyclingbins = array();
	while($row = wesql::fetch_assoc($request)){
		$recyclingbins[$row['id_recyclingboard']] = explode(',',$row['boards']);
	}
	return $recyclingbins;
}

function get_board_names($update = false){
	global $context;
	if(isset($context['board_names']) && $update = false)
		return;


	$context['board_names'] = array();
	$request = wesql::query('SELECT id_board, name FROM {db_prefix}boards', array());
	while($row = wesql::fetch_assoc($request)){
		$context['board_names'][$row['id_board']] = $row['name'];
	}


}

function find_recyclingboard($recyclingboardarr, $child_id){
	foreach($recyclingboardarr as $rid => $childs){
		foreach($childs as $c){
			if($c == $child_id)
				return $rid;
		}

	}
	return false;


}
