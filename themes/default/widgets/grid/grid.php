<script type="text/javascript">

function upgrade_grid(obj) {

	if (obj.find('textarea').length) {
		return;
	}
	id = obj.attr('id');
	
	var value;
	var input;
	var height;
	var width;
	var a_id;
	var next_id;
	var prev_id;
	
	/* TODO: I don't use this var at the moment */
	var num_cols = <?php echo count($table_cols); ?>;
	
	value = obj.text();
	height = obj.height();
	width = obj.width();
	
	obj.text("");
	
	input = $("<textarea class='p4a_grid_text' id='"+id+"_text' type='text' ></textarea>");				
	input.val(value);
	input.height(height);
	input.width(width);
	
	input.focus (function(){
		id = $(this).attr('id');
		a_id = id.split('_');
	
		next_id = a_id[0] + '_' + a_id[1] + '_' + (Number(a_id[2])+1);
		prev_id = a_id[0] + '_' + a_id[1] + '_' + (Number(a_id[2])-1);
	
		upgrade_grid($(prev_id));
		
		//TODO:Fix this
		upgrade_grid($("td:eq("+(Number(a_id[2])+1)+")"));
		upgrade_grid($("td:eq("+(Number(a_id[2])-1)+")"));
	});
	
	input.change(function(){
		id = $(this).attr('id');
		a_id = id.split('_');
		pk_value = a_id[1];
		field_name = a_id[3];
		p4a_event_execute(a_id[0],'prechange',pk_value,field_name,input.val());
	});
	
	obj.append(input);
}

$(document).ready(function() {
	$("td").each(function() {
		$(this).click(function(){
			if (!$(this).find('textarea').length) {
				upgrade_grid($(this));
			} 
		});
		
	});
});

</script>
		
<table id="<?php echo $this->getId() ?>" <?php echo $this->composeStringClass() ?> <?php echo $this->composeStringProperties() ?>>
	<?php if ($this->getLabel()): ?>
	<caption><?php echo __($this->getLabel()) ?></caption>
	<?php endif; ?>

	<?php if ($this->_show_row_indicator): ?>
	<col class="select" />
	<?php endif; ?>
	<?php foreach ($table_cols as $col): ?>
	<col <?php echo $col['properties'] ?> />
	<?php endforeach; ?>

	<?php if (@$headers): ?>
	<thead>
		<tr>
			<?php foreach ($headers as $header): ?>
			<?php if ($header['action']): ?>
				<th>
					<?php if ($header['order'] == 'asc'): ?>
						<div style="float:right">&#x25BC;</div>
					<?php elseif ($header['order'] == 'desc'): ?>
						<div style="float:right">&#x25B2;</div>
					<?php endif; ?>
					<a href="#" <?php echo $header['action']?>><?php echo $header['value']?></a>
				</th>
			<?php else: ?>
				<th>
					<?php if ($header['order'] == 'asc'): ?>
						<div style="float:right">&#x25BC;</div>
					<?php elseif ($header['order'] == 'desc'): ?>
						<div style="float:right">&#x25B2;</div>
					<?php endif; ?>
					<?php echo $header['value']?>
				</th>
			<?php endif; ?>
			<?php endforeach; ?>
		</tr>
	</thead>
	<?php endif; ?>

	<?php if (!empty($table_rows)): ?>
	<tbody <?php echo $this->rows->composeStringProperties()?> <?php echo $this->rows->composeStringClass()?>>
		<?php $i = 0; ?>
		<?php foreach ($table_rows as $row): ?>
			<?php $i++; ?>
			<tr>
				<?php foreach ($row['cells'] as $cell): ?>
					<td id="<?php echo $cell['id'] ?>" class="<?php echo $cell['class'] ?> <?php echo $cell['type']?>"><?php echo $cell['value']?></td>
				<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
	</tbody>
	<?php endif; ?>
	<?php if (isset($navigation_bar)): ?>
		<tr><th colspan='99' class="p4a_toolbar"><?php echo $navigation_bar ?></th></tr>
	<?php endif; ?>
</table>