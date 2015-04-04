<?php

function template_mrb_main(){
	global $context, $txt;
	echo '	<div id="admincenter">
		
			<we:cat>
				RecyclingBoards
			</we:cat>
			<div class="windowbg wrc">';
	template_generic_error_success();

        echo '      		<div class="w100 windowbg wrc"> 
					<table class="table_grid cs0" style="width: 100%">
						<thead>
							<tr class="catbg">
								<th><a href="" rel="nofollow">'.$txt['mrb_recyclingboard'].'</a></th>
								<th><a href="" rel="nofollow">'.$txt['mrb_childboards'].'</a></th>
                                        		        <th><a href="" rel="nofollow">'.$txt['mrb_modify'].'</a></th>
								<th class="center"></th>
							</tr>
						</thead>
						<tbody>';
	foreach($context['mrb_recyclingbins'] as $recyclingboard_id => $child_ids){
		$childs = array();
		foreach($child_ids as $cid){
			#log_error(print_r(array_search($cid, $context['board_listing'], true)));
			#$childs[] = array_search($cid, $context['board_listing']);
			$childs[] = $context['board_names'][$cid];
		}
		echo '<tr class="windowbg" id="list_member_list_0">
							<td class="center">'. $context['board_names'][$recyclingboard_id].'</td>
							<td>'.implode(", ",$childs).'</td>
							<td class="center"><a href="<URL>?action=admin;area=morerecyclingbins;sa=delete&id='.$recyclingboard_id.'">'.$txt['mrb_delete'].'</a><br><a href="<URL>?action=admin;area=morerecyclingbins;sa=modify&id='.$recyclingboard_id.'">'.$txt['mrb_modify'].'</a></td>
						</tr>';	

	}

	echo '
                                </tbody>
                               </table>
                </div><div class="right"><input type="submit" value="'.$txt['mrb_add'].'" onclick="window.location.href = \'index.php?action=admin;area=topuploads;sa=add\'"></input></div>
			</div>
	</div>
	<br class="clear">';

}

function template_generic_error_success(){
	global $context;
	if(!empty($context['mrb_errors'])){
		echo '<div class="errorbox" id="errors">
					<h3 id="error_serious">'.$context['mrb_error_label'].'</h3>
					<ul class="error" id="error_list">';
		foreach($context['mrb_errors'] as $e)
			echo '<li>'.$e.'</li>';
		echo '			</ul>
				</div>';
	}

	if(!empty($context['mrb_success'])){
		echo '<div class="windowbg" id="profile_success">';
		foreach($context['mrb_success'] as $s)
			echo $s."<br>";
		echo '</div>';
	}

}

function template_callback_select_board(){
	global $context, $txt, $config_var;
	echo '<dt>'.$txt['mrb_recyclingboard'].'</dt>';

	echo '<dd>';
	
	#add_js('new SelectBoard("select_board");');

	echo '<select name="select_board" id="select_board">';
	if(empty($context['mrb_edit']))
		echo '<option data-hide>=> '.$txt['select_board'].'</option>';

	foreach ($context['board_listing'] as $cat_id => $cat)
	{
		echo '<optgroup label="'.$cat["name"].'">';
		foreach ($cat['boards'] as $id_board => $board){
				echo '<option value="'.$id_board.'"';
				if(!empty($context['mrb_edit']) && isset($context['mrb_edit']['id']) && $context['mrb_edit']['id'] == $id_board)					echo ' selected';
				echo '>'.str_repeat('&nbsp; &nbsp; ', $board[0]).$board[1].'</option>';
		}
	}
	echo '</select>';
	echo '</dd>';

}

function template_callback_success_error_box(){
	template_generic_error_success();

}
