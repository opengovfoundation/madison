Annotator.Plugin.Madison = function(element){
	var madison = {};

	madison.pluginInit = function(){
		this.annotator.viewer.addField({
			load: function(field, annotation){
				console.log(annotation.id);
				field.innerHTML = '<a href="' + window.location.origin + '/note/' + annotation.id + '">View Note</a>';
			}
		})
	}

	return madison;
}