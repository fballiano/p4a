    <style type="text/css" media="screen">
        <!-- @import url("[[$tpl_path]]/css/menuDropdown.css"); -->
    </style>
    <script type="text/javascript" src="[[$tpl_path]]/js/menuDropdown.js"></script>
      <ul name="menu_top" class="menuList" [[$properties]]>
         [[foreach item=item from=$items]] 
        <li class="menubar">
          [[if $item.actions]]
		  <a href="#" id="[[$item.id]]" class="actuator" [[$item.actions]] [[$item.properties]]>[[$item.label]]</a>
          [[else]]
          <a href="#" id="[[$item.id]]" class="actuator" [[$item.properties]]>[[$item.label]]</a>
          [[/if]]
          <ul id="[[$item.id]]Menu" class="menu">
          [[foreach item=sub_item from=$item.sub_items]]
            <li><a href="#" [[$sub_item.actions]] [[$sub_item.properties]]>[[$sub_item.label]]</a></li>
          [[/foreach]]
          </ul>
        </li>
        [[/foreach]]
      </ul>
    <script type="text/javascript">
		[[foreach item=item from=$items]] 
			[[if not $item.actions]]
        	initializeMenu("[[$item.id]]Menu", "[[$item.id]]");
        	[[/if]]
        [[/foreach]]
    </script>
    