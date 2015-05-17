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
                </div><div class="right"><input type="submit" value="'.$txt['mrb_add'].'" onclick="window.location.href = \'index.php?action=admin;area=morerecyclingbins;sa=add\'"></input></div>
			</div>
	</div>
	<br class="clear">';

}
function template_mrb_modify(){
	global $context, $txt;
	echo '<form action="/wedge/index.php?action=admin;area=morerecyclingbins;sa='.$context['mrb_modify']['type'].';save'.($context['mrb_modify']['type'] == 'modify' ? '&id='.$context['mrb_modify']['recyclingboard'] : '').'" method="post" accept-charset="UTF-8">
			<div class="windowbg2 wrc">
				<dl class="settings">';

	// Error displaying
	template_generic_error_success();

	// Choose recyclingboard
	echo '				<dt>'.$txt['mrb_recyclingboard'].'</dt>';

	echo '				<dd>';
	
	echo '					<select name="select_board" id="select_board">';
	if(empty($context['mrb_modify']['recyclingboard']))
		echo '					<option data-hide>=> '.$txt['select_board'].'</option>';

	foreach ($context['board_listing'] as $cat_id => $cat)
	{
		echo '					<optgroup label="'.$cat["name"].'">';
		foreach ($cat['boards'] as $id_board => $board){
				echo '				<option value="'.$id_board.'"';
				if(isset($context['mrb_modify']['recyclingboard']) && $context['mrb_modify']['recyclingboard'] == $id_board)
					echo ' selected';
				echo '>'.str_repeat('&nbsp; &nbsp; ', $board[0]).$board[1].'</option>';
		}
	}
	echo '					</select>
					</dd>';

	// Select affected boards - ripped from Admin.template.php
	echo '				<dt>'.$txt['mrb_childboards'].'</dt>';
	echo '				<dd>';
	echo '
						<fieldset id="fs_affected_boards">
							<legend><a href="#" onclick="$(\'#fs_affected_boards\').hide(); $(\'#fs_affected_boards_link\').show(); return false;">', $txt['select_from_list'], '</a></legend>';

					foreach ($context['board_listing'] as $cat_id => $cat)
					{
						echo '
							<label><strong>', $cat['name'], '</strong> <input type="checkbox" id="catsel', $cat_id, '" onclick="selectcat(', $cat_id, ');"></label>
							<ul class="permission_groups">';

						foreach ($cat['boards'] as $id_board => $board)
							echo '
								<li>&nbsp; ', $board[0] > 0 ? str_repeat('&nbsp; &nbsp; ', $board[0]) : '', '<label><input type="checkbox" class="cat', $cat_id, '" name="affected_boards[', $id_board, ']" value="on"', !empty($context['mrb_modify']['affected_boards']) && in_array($id_board, $context['mrb_modify']['affected_boards']) ? ' checked' : '', '> ', $board[1], '</label></li>';

						echo '
							</ul>';
					}

					add_js('
	function selectcat(id)
	{
		$(".cat" + id).prop("checked", $("#catsel" + id).prop("checked"));
	};');

					echo '
						</fieldset>
						<a href="#" onclick="$(\'#fs_affected_boards\').show(); $(\'#fs_affected_boards_link\').hide(); return false;" id="fs_affected_boards_link" class="hide">[ ', $txt['click_to_see_more'], ' ]</a>';

					add_js('$("#fs_affected_boards").hide(); $("#fs_affected_boards_link").show();');
	echo '				</dd>';

	echo '			</dl>';
		echo '
			<div class="right padding">
				<input type="submit" value="', $txt['save'],'" class="submit">
			</div>';

	echo '
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
		</form>';
	echo '	</div>
	      </form>';
}

/*
 * Show Success/Errors
 */
function template_generic_error_success(){
	global $context;
	if(!empty($context['mrb_errors'])){
		echo '			<div class="errorbox" id="errors">
						<h3 id="error_serious">'.$context['mrb_error_label'].'</h3>
						<ul class="error" id="error_list">';
		foreach($context['mrb_errors'] as $e)
			echo '				<li>'.$e.'</li>';
		echo '				</ul>
					</div>';
	}

	if(!empty($context['mrb_success'])){
		echo '<div class="windowbg" id="profile_success">';
		foreach($context['mrb_success'] as $s)
			echo $s."<br>";
		echo '</div>';
	}
}

