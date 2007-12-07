<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=<?php echo $_charset?>" />
<title><?php echo $_title?></title>

<?php foreach ($_javascript as $_k=>$_v): ?>
<script type="text/javascript" src="<?php echo $_k?>"></script>
<?php endforeach; ?>

<?php foreach ($_css as $_url=>$_media): ?>
<link href="<?php echo $_url?>" rel="stylesheet" type="text/css" media="<?php echo join(', ', array_keys($_media))?>"></link>
<?php endforeach; ?>

<script type="text/javascript">

Ext.BLANK_IMAGE_URL = '<?php echo P4A_THEME_PATH ?>/extjs/resources/images/default/s.gif';

Ext.onReady(function() {
	Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
	Ext.QuickTips.init();
	
	
	
	
	
	
	
    var myData = [
        ['3m Co',71.72,0.02,0.03,'9/1 12:00am'],
        ['Alcoa Inc',29.01,0.42,1.47,'9/1 12:00am'],
        ['Altria Group Inc',83.81,0.28,0.34,'9/1 12:00am'],
        ['American Express Company',52.55,0.01,0.02,'9/1 12:00am'],
        ['American International Group, Inc.',64.13,0.31,0.49,'9/1 12:00am'],
        ['AT&T Inc.',31.61,-0.48,-1.54,'9/1 12:00am'],
        ['Boeing Co.',75.43,0.53,0.71,'9/1 12:00am'],
        ['Caterpillar Inc.',67.27,0.92,1.39,'9/1 12:00am'],
        ['Citigroup, Inc.',49.37,0.02,0.04,'9/1 12:00am'],
        ['E.I. du Pont de Nemours and Company',40.48,0.51,1.28,'9/1 12:00am'],
        ['Exxon Mobil Corp',68.1,-0.43,-0.64,'9/1 12:00am'],
        ['General Electric Company',34.14,-0.08,-0.23,'9/1 12:00am'],
        ['General Motors Corporation',30.27,1.09,3.74,'9/1 12:00am'],
        ['Hewlett-Packard Co.',36.53,-0.03,-0.08,'9/1 12:00am'],
        ['Honeywell Intl Inc',38.77,0.05,0.13,'9/1 12:00am'],
        ['Intel Corporation',19.88,0.31,1.58,'9/1 12:00am'],
        ['International Business Machines',81.41,0.44,0.54,'9/1 12:00am'],
        ['Johnson & Johnson',64.72,0.06,0.09,'9/1 12:00am'],
        ['JP Morgan & Chase & Co',45.73,0.07,0.15,'9/1 12:00am'],
        ['McDonald\'s Corporation',36.76,0.86,2.40,'9/1 12:00am'],
        ['Merck & Co., Inc.',40.96,0.41,1.01,'9/1 12:00am'],
        ['Microsoft Corporation',25.84,0.14,0.54,'9/1 12:00am'],
        ['Pfizer Inc',27.96,0.4,1.45,'9/1 12:00am'],
        ['The Coca-Cola Company',45.07,0.26,0.58,'9/1 12:00am'],
        ['The Home Depot, Inc.',34.64,0.35,1.02,'9/1 12:00am'],
        ['The Procter & Gamble Company',61.91,0.01,0.02,'9/1 12:00am'],
        ['United Technologies Corporation',63.26,0.55,0.88,'9/1 12:00am'],
        ['Verizon Communications',35.57,0.39,1.11,'9/1 12:00am'],
        ['Wal-Mart Stores, Inc.',45.45,0.73,1.63,'9/1 12:00am']
    ];
    var store = new Ext.data.SimpleStore({
        fields: [
           {name: 'company'},
           {name: 'price', type: 'float'},
           {name: 'change', type: 'float'},
           {name: 'pctChange', type: 'float'},
           {name: 'lastChange', type: 'date', dateFormat: 'n/j h:ia'}
        ]
    });
    store.loadData(myData);
    var grid = new Ext.grid.GridPanel({
        store: store,
        shadow: true,
        columns: [
            {id:'company',header: "Company", sortable: true, dataIndex: 'company'},
            {header: "Price", sortable: true, dataIndex: 'price'},
            {header: "Change", sortable: true, dataIndex: 'change'},
            {header: "% Change", sortable: true, dataIndex: 'pctChange'},
            {header: "Last Updated", sortable: true, dataIndex: 'lastChange'}
        ],
        stripeRows: true,
        autoExpandColumn: 'company',
        title:'Array Grid'
    });
    
    grid.on('cellClick', function(grid, rowIndex, columnIndex, e) {alert(columnIndex);});
	
	
	
	
	
	
	
	
	
	
	<?php echo $menu->getAsString() ?>
	<?php echo $main->getAsString() ?>
	<?php echo $toolbar->getAsString() ?>
	
	
	
	

    
    
    
	
	
	
	
	new Ext.P4AViewport({
		layout:'border',
		items: [
			{
				region: 'center',
				id: 'p4a-main-region',
				autoScroll: true,
				tbar: <?php echo $toolbar->getId() ?>,
				items: [<?php echo $main->getId() ?>]
			},
			{region: 'north', tbar:<?php echo $menu->getId() ?>, margins:'0 0 5 0', border: false, height: 1},
			{region: 'west', html:'west region', split:true, margins:'0 0 0 5', width: 200,collapsible:true,collapseMode:'mini'},
			//{region: 'east', html:'east region', split:true, margins:'0 5 0 0', width: 200},
			new Ext.BoxComponent({region: 'south', el: 'p4a-footer'})
		]
	});

	Ext.get('<?php echo $main->getId() ?>').applyStyles('margin-top:10px');
});
</script>
</head>
<body>
	<?php echo $this->maskOpen() ?>
	<?php echo $this->maskClose() ?>
	<div id="p4a-footer">
		Powered by <a href="http://p4a.sourceforge.net">P4A - PHP For Applications</a>
		<?php echo P4A_VERSION ?>
	</div>
</body>
</html>