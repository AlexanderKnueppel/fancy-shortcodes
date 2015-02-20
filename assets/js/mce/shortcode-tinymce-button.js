/*function dump(arr,level) {
	var dumped_text = "";
	if(!level) level = 0;
	
	//The padding given at the beginning of the line.
	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "    ";
	
	if(typeof(arr) == 'object') { //Array/Hashes/Objects 
		for(var item in arr) {
			var value = arr[item];
			
			if(typeof(value) == 'object') { //If it is an array,
				dumped_text += level_padding + "'" + item + "' ...\n";
				dumped_text += dump(value,level+1);
			} else {
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
			}
		}
	} else { //Stings/Chars/Numbers etc.
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}*/

(function() {

    tinymce.PluginManager.add('fsh_mce_btn', function( editor )
    {
        var shortcodeValues = [];
		var shortcodeCategories = [];
		var shortcodeGlobal = [];
		
        jQuery.each(shortcodes_button, function(i)
        {
			if( jQuery.inArray(shortcodes_button[i].category, shortcodeCategories) == -1 ) {
				shortcodeCategories.push(shortcodes_button[i].category);
				shortcodeValues[shortcodes_button[i].category] = [];
			}
			
            shortcodeValues[shortcodes_button[i].category].push({
				               text: shortcodes_button[i].tag, 
							   onclick: function() {
                            		tinymce.execCommand('mceInsertContent', false, shortcodes_button[i].content);
                       		   }
			});
        });
		//alert(dump(shortcodeCategories));
		
		jQuery.each(shortcodeCategories, function(i) {
			shortcodeGlobal.push( {
				text: shortcodeCategories[i],
				menu: shortcodeValues[shortcodeCategories[i]]
			});
		});

		
        editor.addButton('fsh_mce_btn', {
			//type: 'listbox',
			text: 'Shortcodes',
			icon: false,
			
			type: 'menubutton',
			menu: shortcodeGlobal
        });
    });
})();