Ext.application({
	name : 'sencha',
	
	launch : function(){
		Ext.create("Ext.TabPanel", {
			fullscreen : true,
			tabBarPosition : 'bottom',
			items : [{
				title : 'Home',
				iconCls : 'home',
				html : [
				        '<img src="http://staging.sencha.com/img/sencha.png" />',
				        '<h1>Welcome to Sencha Touch</h1>',
				        '<p>test</p>',
				        '<h2>Sencha Touch</h2>'
				        ].join("")
			},
			{
				xtype : 'list',
				title : 'Blog',
				iconCls : 'star',
				
				itemTpl : '{title}',
				store : {
					fields : ['title','url'],
					data : [
					        {title : 'Ext Scheduler 2.0', url : 'ext-scheduler-2-0'},
					        {title : 'Ext Scheduler 2.0', url : 'ext-scheduler-2-0'},
					        {title : 'Ext Scheduler 2.0', url : 'ext-scheduler-2-0'},
					        {title : 'Ext Scheduler 2.0', url : 'ext-scheduler-2-0'}
					       ]
				}
			}]
		}).setActiveItem(1);
	}
});





 
