<table style="width:100%" cellpadding="0" cellspacing="0">
  <tr bgcolor="#F7F7FF"> 
    <td colspan="3" background="[[$tpl_path]]/images/menuUP_background.gif">
	   <table  border="0" cellspacing="0" cellpadding="0" >
        <tr>
          <td>&nbsp;&nbsp;</td>	
          [[foreach name=menu item=item from=$items1]] 
			 [[if $item.active ]]
		  <td width="1" height="19" valign="top"><img src="[[$tpl_path]]/images/label_sx_sel.gif" width="8" height="22"></td>
          <td width="140" align="center" valign="middle" background="[[$tpl_path]]/images/label_background_sel.gif" class="mainmenuSel"  nowrap >[[$item.label]]</td>
          <td width="1"><img src="[[$tpl_path]]/images/label_dx_sel.gif" width="8" height="22"></td>
          	 [[else]]
          <td width="1" height="19" valign="top"><img src="[[$tpl_path]]/images/label_sx.gif" width="8" height="22"></td>
          <td width="140" align="center" valign="middle" background="[[$tpl_path]]/images/label_background.gif" class="mainmenuSel" nowrap ><a href="#" [[$item.actions]] class="LinkMainMenu">[[$item.label]]</a></td>          
          <td width="1"><img src="[[$tpl_path]]/images/label_dx.gif" width="8" height="22"></td>
			 [[/if]]	
		  [[/foreach]]	
        </tr>
      </table>
	</td>
  </tr>
  <tr> 
	<td colspan="3" background="[[$tpl_path]]/images/menu_background.gif">
		<table height="22" border="0" cellpadding="0" cellspacing="0">
        <tr> 
          [[foreach name=menu item=sub_item from=$items2]] 
		  	[[if $sub_item.active]]	
          <td align="center" class="mainmenuSel"><font color="#fefefe">&nbsp;&nbsp;[[$sub_item.label]]&nbsp;&nbsp;</font></td>
          	[[else]]
          <td align="center" class="mainmenuSel">&nbsp;&nbsp;<a href="#" class="LinkSecondMenu" [[$sub_item.actions]]>[[$sub_item.label]]</a>&nbsp;&nbsp;</td>
          	[[/if]]
          <td width="1"><img src="[[$tpl_path]]/images/separatore_menu.gif" width="5" height="15"></td>
          [[foreachelse]]
		   <td>&nbsp;</td>	          	
          [[/foreach]] 
        </tr>
     </table>
  </tr>
</table>  